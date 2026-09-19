# PDF2MD — Document to Markdown Converter

A Flask web application that converts **PDF** and **PPTX** files to clean **Markdown** format.

## Features

- 📄 Convert PDF files to Markdown
- 📊 Convert PowerPoint (PPTX) files to Markdown
- 🧠 Gemini AI mode for OCR with math equation support (LaTeX)
- ⚡ Fast local conversion (no internet needed)
- 📋 Copy to clipboard & download `.md` output

## Setup

### 1. Clone the repository

```bash
git clone https://github.com/YOUR_USERNAME/PDF2MD.git
cd PDF2MD
```

### 2. Create a virtual environment & install dependencies

```bash
python -m venv venv
source venv/bin/activate   # On Windows: venv\Scripts\activate
pip install -r requirements.txt
```

### 3. Configure environment variables

Create a `.env` file in the root directory:

```
GEMINI_API_KEY=your_google_gemini_api_key_here
```

> ⚠️ The Gemini API key is only required if you want to use the AI-powered OCR mode. Fast mode works without it.

### 4. Run the app

```bash
python app.py
```

Open your browser at **http://127.0.0.1:5000**

## Supported File Types

| Format | Extension |
|--------|-----------|
| PDF | `.pdf` |
| PowerPoint | `.pptx` |

## Tech Stack

- **Backend:** Python, Flask, PyMuPDF, python-pptx
- **AI:** Google Gemini Vision API
- **Frontend:** HTML, CSS, JavaScript
