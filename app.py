"""
PDF2MD — Document to Markdown Converter
Flask web application that converts PDF and PPTX files to Markdown format.
"""

import os
import re
import json
import io
import base64
import tempfile
import subprocess
import shutil
from flask import Flask, render_template, request, jsonify
import fitz  # PyMuPDF
from pptx import Presentation
from pptx.util import Pt
from dotenv import load_dotenv
from google import genai
from google.genai import types

# Load environment variables
load_dotenv()

app = Flask(__name__)
app.config['MAX_CONTENT_LENGTH'] = 100 * 1024 * 1024  # 100 MB max upload

# Initialize Gemini client
gemini_client = None
GEMINI_API_KEY = os.getenv('GEMINI_API_KEY')
if GEMINI_API_KEY:
    gemini_client = genai.Client(api_key=GEMINI_API_KEY)

# ─── Conversion Statistics (in-memory counter) ───────────────────────────────
conversion_stats = {
    'total_conversions': 0,
    'total_pages_processed': 0,
}


# ─── PDF to Markdown Conversion Logic ────────────────────────────────────────

def classify_block_type(block, median_size):
    """Classify a text block as heading, paragraph, list item, etc."""
    if not block.get("lines"):
        return "paragraph"

    first_line = block["lines"][0]
    if not first_line.get("spans"):
        return "paragraph"

    first_span = first_line["spans"][0]
    font_size = first_span.get("size", 12)
    text = first_span.get("text", "").strip()

    # Detect list items
    if re.match(r'^[\u2022\u2023\u25E6\u2043\u2219•●○◦‣⁃]\s', text):
        return "list_item"
    if re.match(r'^\d+[\.\)]\s', text):
        return "ordered_list_item"

    # Detect headings by font size relative to the median
    if font_size > median_size * 1.6:
        return "h1"
    elif font_size > median_size * 1.3:
        return "h2"
    elif font_size > median_size * 1.1:
        return "h3"

    return "paragraph"


def spans_to_markdown(spans):
    """Convert a list of spans (with font info) to Markdown text."""
    parts = []
    for span in spans:
        text = span.get("text", "")
        if not text:
            continue

        flags = span.get("flags", 0)
        is_bold = bool(flags & 2**4)     # bit 4 = bold
        is_italic = bool(flags & 2**1)   # bit 1 = italic

        if is_bold and is_italic:
            parts.append(f"***{text}***")
        elif is_bold:
            parts.append(f"**{text}**")
        elif is_italic:
            parts.append(f"*{text}*")
        else:
            parts.append(text)

    return "".join(parts)


def convert_pdf_to_markdown(pdf_path):
    """
    Convert a PDF file to Markdown format.
    Returns (markdown_string, page_count).
    """
    doc = fitz.open(pdf_path)
    page_count = len(doc)
    markdown_lines = []

    # First pass: calculate median font size across all pages
    all_font_sizes = []
    for page in doc:
        blocks = page.get_text("dict", flags=fitz.TEXT_PRESERVE_WHITESPACE)["blocks"]
        for block in blocks:
            if block.get("type") != 0:  # Skip non-text blocks (images, etc.)
                continue
            for line in block.get("lines", []):
                for span in line.get("spans", []):
                    size = span.get("size", 12)
                    text = span.get("text", "").strip()
                    if text:
                        all_font_sizes.append(size)

    median_size = sorted(all_font_sizes)[len(all_font_sizes) // 2] if all_font_sizes else 12

    # Second pass: convert blocks to markdown
    for page_num, page in enumerate(doc):
        if page_num > 0:
            markdown_lines.append("\n---\n")  # Page separator

        blocks = page.get_text("dict", flags=fitz.TEXT_PRESERVE_WHITESPACE)["blocks"]

        for block in blocks:
            # Handle image blocks
            if block.get("type") == 1:
                markdown_lines.append("\n![image](embedded-image)\n")
                continue

            if block.get("type") != 0:
                continue

            block_type = classify_block_type(block, median_size)

            # Collect all spans across all lines in this block
            all_spans_text = []
            for line in block.get("lines", []):
                line_md = spans_to_markdown(line.get("spans", []))
                all_spans_text.append(line_md)

            full_text = " ".join(all_spans_text).strip()

            if not full_text:
                continue

            # Format based on block type
            if block_type == "h1":
                markdown_lines.append(f"\n# {full_text}\n")
            elif block_type == "h2":
                markdown_lines.append(f"\n## {full_text}\n")
            elif block_type == "h3":
                markdown_lines.append(f"\n### {full_text}\n")
            elif block_type == "list_item":
                # Remove bullet character
                cleaned = re.sub(r'^[\u2022\u2023\u25E6\u2043\u2219•●○◦‣⁃]\s*', '', full_text)
                markdown_lines.append(f"- {cleaned}")
            elif block_type == "ordered_list_item":
                markdown_lines.append(full_text)
            else:
                markdown_lines.append(f"\n{full_text}\n")

    doc.close()

    # Clean up excessive blank lines
    result = "\n".join(markdown_lines)
    result = re.sub(r'\n{4,}', '\n\n\n', result)
    result = result.strip()

    return result, page_count


# ─── Gemini Vision API PDF to Markdown (with Math support) ───────────────────

def convert_pdf_with_gemini(pdf_path):
    """
    Convert a PDF file to Markdown using Google Gemini Vision API.
    Renders each page as an image, sends to Gemini for OCR with math support.
    Returns (markdown_string, page_count).
    """
    if not gemini_client:
        raise RuntimeError(
            'Gemini API key not configured. '
            'Set GEMINI_API_KEY in your .env file.'
        )

    doc = fitz.open(pdf_path)
    page_count = len(doc)
    all_pages_md = []

    MATH_PROMPT = """You are a document OCR assistant. Extract ALL text and mathematical content from this page image.

Rules:
- Output clean Markdown format
- For ALL math equations (inline or block), use LaTeX notation:
  - Inline math: $...$ (e.g., $x^2 + y^2 = r^2$)
  - Block/display math: $$...$$ (e.g., $$\\iint_D f(x,y) \\, dx \\, dy$$)
- Preserve headings, lists, tables, and paragraph structure
- Do NOT add any commentary or explanation — only output the page content
- If there are images or diagrams, describe them briefly in [brackets]
- Preserve the reading order of the document"""

    for page_num in range(page_count):
        page = doc[page_num]

        # Render page as high-res PNG image
        mat = fitz.Matrix(2.0, 2.0)  # 2x zoom for better quality
        pix = page.get_pixmap(matrix=mat)
        img_bytes = pix.tobytes("png")

        # Send image to Gemini
        response = gemini_client.models.generate_content(
            model="gemini-3.6-flash",
            contents=[
                types.Content(
                    role="user",
                    parts=[
                        types.Part.from_bytes(data=img_bytes, mime_type="image/png"),
                        types.Part.from_text(text=MATH_PROMPT),
                    ]
                )
            ],
        )

        page_md = response.text.strip() if response.text else ''

        if page_md:
            all_pages_md.append(page_md)

    doc.close()

    # Combine all pages with page separators
    combined = '\n\n---\n\n'.join(all_pages_md)
    return combined.strip(), page_count



# ─── PPTX to Markdown Conversion Logic ───────────────────────────────────────

def convert_pptx_to_markdown(pptx_path):
    """
    Convert a PPTX (PowerPoint) file to Markdown format.
    Returns (markdown_string, slide_count).
    """
    prs = Presentation(pptx_path)
    slide_count = len(prs.slides)
    markdown_lines = []

    for slide_num, slide in enumerate(prs.slides, 1):
        if slide_num > 1:
            markdown_lines.append("\n---\n")  # Slide separator

        markdown_lines.append(f"\n## Slide {slide_num}\n")

        for shape in slide.shapes:
            # Handle tables
            if shape.has_table:
                table = shape.table
                rows = []
                for row in table.rows:
                    cells = [cell.text.strip() for cell in row.cells]
                    rows.append(cells)

                if rows:
                    # Header row
                    markdown_lines.append("\n| " + " | ".join(rows[0]) + " |")
                    markdown_lines.append("| " + " | ".join(["---"] * len(rows[0])) + " |")
                    # Data rows
                    for row in rows[1:]:
                        markdown_lines.append("| " + " | ".join(row) + " |")
                    markdown_lines.append("")
                continue

            # Handle text frames
            if not shape.has_text_frame:
                continue

            for paragraph in shape.text_frame.paragraphs:
                text = paragraph.text.strip()
                if not text:
                    continue

                # Build text with inline formatting
                parts = []
                for run in paragraph.runs:
                    run_text = run.text
                    if not run_text:
                        continue

                    is_bold = run.font.bold
                    is_italic = run.font.italic

                    if is_bold and is_italic:
                        parts.append(f"***{run_text}***")
                    elif is_bold:
                        parts.append(f"**{run_text}**")
                    elif is_italic:
                        parts.append(f"*{run_text}*")
                    else:
                        parts.append(run_text)

                formatted_text = "".join(parts) if parts else text

                # Detect heading-like text (title shapes or large font)
                is_title = False
                if paragraph.runs:
                    font_size = paragraph.runs[0].font.size
                    if font_size and font_size >= Pt(24):
                        is_title = True

                # Check if shape is a title placeholder
                try:
                    if shape.placeholder_format is not None:
                        ph_idx = shape.placeholder_format.idx
                        if ph_idx in (0, 1):  # Title or Center Title
                            is_title = True
                except ValueError:
                    pass  # Shape is not a placeholder, skip

                # Detect bullet points by indentation level
                indent_level = paragraph.level if paragraph.level else 0

                if is_title:
                    markdown_lines.append(f"\n### {formatted_text}\n")
                elif indent_level > 0:
                    indent = "  " * (indent_level - 1)
                    markdown_lines.append(f"{indent}- {formatted_text}")
                else:
                    # Check if it looks like a bullet point
                    if formatted_text.startswith(('•', '●', '○', '◦', '▪', '▸')):
                        cleaned = formatted_text.lstrip('•●○◦▪▸ ')
                        markdown_lines.append(f"- {cleaned}")
                    else:
                        markdown_lines.append(f"\n{formatted_text}\n")

        # Extract slide notes
        if slide.has_notes_slide and slide.notes_slide.notes_text_frame:
            notes_text = slide.notes_slide.notes_text_frame.text.strip()
            if notes_text:
                markdown_lines.append(f"\n> **Notes:** {notes_text}\n")

    # Clean up excessive blank lines
    result = "\n".join(markdown_lines)
    result = re.sub(r'\n{4,}', '\n\n\n', result)
    result = result.strip()

    return result, slide_count


# ─── Flask Routes ────────────────────────────────────────────────────────────

@app.route('/')
def index():
    """Serve the main page."""
    return render_template('index.html', stats=conversion_stats)


@app.route('/upload', methods=['POST'])
def upload():
    """Handle file upload and convert to Markdown."""
    if 'file' not in request.files:
        return jsonify({'error': 'No file uploaded'}), 400

    file = request.files['file']

    if file.filename == '':
        return jsonify({'error': 'No file selected'}), 400

    filename_lower = file.filename.lower()
    supported_extensions = ('.pdf', '.pptx')
    if not filename_lower.endswith(supported_extensions):
        return jsonify({'error': 'Only PDF and PPTX files are supported'}), 400

    # Get engine choice from form data (default: 'fast')
    engine = request.form.get('engine', 'fast')

    try:
        # Determine file type
        file_ext = os.path.splitext(file.filename)[1].lower()

        # Save to a temp file
        with tempfile.NamedTemporaryFile(delete=False, suffix=file_ext) as tmp:
            file.save(tmp.name)
            tmp_path = tmp.name

        # Convert based on file type and engine
        if file_ext == '.pptx':
            markdown_content, page_count = convert_pptx_to_markdown(tmp_path)
            file_type = 'PPTX'
        elif engine == 'marker':
            markdown_content, page_count = convert_pdf_with_gemini(tmp_path)
            file_type = 'PDF'
        else:
            markdown_content, page_count = convert_pdf_to_markdown(tmp_path)
            file_type = 'PDF'

        # Update stats
        conversion_stats['total_conversions'] += 1
        conversion_stats['total_pages_processed'] += page_count

        # Cleanup temp file
        os.unlink(tmp_path)

        # Generate output filename
        output_filename = os.path.splitext(file.filename)[0] + '.md'

        return jsonify({
            'success': True,
            'markdown': markdown_content,
            'filename': output_filename,
            'pages': page_count,
            'file_type': file_type,
            'engine': engine,
            'stats': conversion_stats,
        })

    except Exception as e:
        import traceback
        traceback.print_exc()
        return jsonify({'error': f'Conversion failed: {str(e)}'}), 500


@app.route('/stats')
def stats():
    """Return current conversion statistics."""
    return jsonify(conversion_stats)


# ─── Run ─────────────────────────────────────────────────────────────────────

if __name__ == '__main__':
    app.run(debug=True, port=5000)
