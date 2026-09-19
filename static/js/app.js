/**
 * PDF2MD — Client-side JavaScript
 * Handles drag-and-drop upload, file validation, API calls,
 * Markdown preview rendering, copy/download actions.
 */

document.addEventListener('DOMContentLoaded', () => {

    // ─── DOM Elements ────────────────────────────────────────────────────
    const uploadArea       = document.getElementById('upload-area');
    const uploadCard       = document.getElementById('upload-card');
    const fileInput        = document.getElementById('file-input');
    const progressWrapper  = document.getElementById('progress-wrapper');
    const progressFilename = document.getElementById('progress-filename');
    const progressStatus   = document.getElementById('progress-status');
    const progressBarFill  = document.getElementById('progress-bar-fill');

    const resultCard       = document.getElementById('result-card');
    const resultTitle      = document.getElementById('result-title');
    const resultMeta       = document.getElementById('result-meta');
    const markdownRaw      = document.getElementById('markdown-raw');
    const markdownPreview  = document.getElementById('markdown-preview');

    const btnCopy          = document.getElementById('btn-copy');
    const btnDownload      = document.getElementById('btn-download');
    const btnNew           = document.getElementById('btn-new');

    const tabBtns          = document.querySelectorAll('.tab-btn');
    const tabPanels        = document.querySelectorAll('.tab-panel');

    const statConversions  = document.getElementById('stat-conversions-num');
    const statPages        = document.getElementById('stat-pages-num');

    const navbar           = document.getElementById('navbar');

    // ─── Engine Toggle ───────────────────────────────────────────────────
    const engineBtns       = document.querySelectorAll('.engine-btn');
    const engineSlider     = document.getElementById('engine-slider');
    const engineInfo       = document.getElementById('engine-info');
    const apiKeyWrapper    = document.getElementById('api-key-wrapper');
    const apiKeyInput      = document.getElementById('gemini-api-key');
    const apiKeyToggle     = document.getElementById('api-key-toggle');

    let selectedEngine = 'fast';

    engineBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const engine = btn.dataset.engine;
            selectedEngine = engine;

            // Update active state
            engineBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            // Slide the toggle indicator
            if (engine === 'marker') {
                engineSlider.classList.add('right');
                engineInfo.classList.add('show');
                apiKeyWrapper.classList.add('show');
            } else {
                engineSlider.classList.remove('right');
                engineInfo.classList.remove('show');
                apiKeyWrapper.classList.remove('show');
            }
        });
    });

    // ─── API Key Show/Hide Toggle ─────────────────────────────────────────
    if (apiKeyToggle) {
        apiKeyToggle.addEventListener('click', () => {
            const isPassword = apiKeyInput.type === 'password';
            apiKeyInput.type = isPassword ? 'text' : 'password';
            apiKeyToggle.querySelector('svg').innerHTML = isPassword
                ? `<path d="M2 2l12 12M6.5 6.6A3 3 0 0111.4 9.5M5.1 5.1C3.6 6.2 2.5 7.8 1 8s4 5 7 5c1.5 0 2.9-.5 4-1.4M9.9 3.1C9.3 3 8.7 3 8 3 5 3 2 8 2 8s.8 1.3 2.1 2.4" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>`
                : `<path d="M8 3C4 3 1 8 1 8s3 5 7 5 7-5 7-5-3-5-7-5z" stroke="currentColor" stroke-width="1.4"/><circle cx="8" cy="8" r="2" stroke="currentColor" stroke-width="1.4"/>`;
        });
    }

    // ─── State ───────────────────────────────────────────────────────────
    let currentMarkdown = '';
    let currentFilename = 'output.md';

    // ─── Navbar Scroll Effect ────────────────────────────────────────────
    window.addEventListener('scroll', () => {
        if (window.scrollY > 10) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });

    // ─── Drag & Drop ────────────────────────────────────────────────────
    ['dragenter', 'dragover'].forEach(event => {
        uploadArea.addEventListener(event, (e) => {
            e.preventDefault();
            e.stopPropagation();
            uploadArea.classList.add('drag-over');
        });
    });

    ['dragleave', 'drop'].forEach(event => {
        uploadArea.addEventListener(event, (e) => {
            e.preventDefault();
            e.stopPropagation();
            uploadArea.classList.remove('drag-over');
        });
    });

    uploadArea.addEventListener('drop', (e) => {
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            handleFile(files[0]);
        }
    });

    // ─── Click to Upload ─────────────────────────────────────────────────
    uploadArea.addEventListener('click', (e) => {
        // Only trigger if not clicking the label/button directly
        if (e.target.tagName !== 'LABEL' && !e.target.closest('.btn-upload')) {
            fileInput.click();
        }
    });

    fileInput.addEventListener('change', () => {
        if (fileInput.files.length > 0) {
            handleFile(fileInput.files[0]);
        }
    });

    // ─── File Handler ────────────────────────────────────────────────────
    function handleFile(file) {
        // Validate file type
        const validExtensions = ['.pdf', '.pptx'];
        const fileName = file.name.toLowerCase();
        const hasValidExt = validExtensions.some(ext => fileName.endsWith(ext));
        if (!hasValidExt) {
            showError('Please upload a PDF or PPTX file.');
            return;
        }

        // Validate file size (100 MB)
        if (file.size > 100 * 1024 * 1024) {
            showError('File size exceeds 100 MB limit.');
            return;
        }

        // Validate Gemini API key is provided when using Math Mode
        if (selectedEngine === 'marker') {
            const key = apiKeyInput ? apiKeyInput.value.trim() : '';
            if (!key) {
                showError('Please enter your Gemini API key to use Math Mode.');
                apiKeyInput && apiKeyInput.focus();
                return;
            }
        }

        uploadFile(file);
    }

    function showError(message) {
        // Create and show error toast
        const toast = document.createElement('div');
        toast.className = 'error-toast';
        toast.innerHTML = `
            <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                <circle cx="9" cy="9" r="8" stroke="#EF4444" stroke-width="1.5"/>
                <path d="M9 5v4M9 12h.01" stroke="#EF4444" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
            <span>${message}</span>
        `;
        toast.style.cssText = `
            position: fixed;
            top: 80px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 24px;
            background: #FEF2F2;
            color: #DC2626;
            border: 1px solid #FECACA;
            border-radius: 10px;
            font-size: 0.9rem;
            font-weight: 500;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            z-index: 9999;
            animation: fadeInUp 0.3s ease;
        `;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transition = 'opacity 0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // ─── Upload & Convert ────────────────────────────────────────────────
    async function uploadFile(file) {
        // Show progress
        progressWrapper.classList.add('show');
        progressFilename.textContent = file.name;
        progressStatus.textContent = 'Uploading...';
        progressBarFill.style.width = '20%';

        // Hide previous result
        resultCard.classList.remove('show');

        const formData = new FormData();
        formData.append('file', file);
        formData.append('engine', selectedEngine);

        // Attach user's Gemini API key if in Math Mode
        if (selectedEngine === 'marker' && apiKeyInput) {
            formData.append('gemini_api_key', apiKeyInput.value.trim());
        }

        try {
            progressBarFill.style.width = '40%';
            const fileType = file.name.toLowerCase().endsWith('.pptx') ? 'PPTX' : 'PDF';

            if (selectedEngine === 'marker') {
                progressStatus.textContent = `Converting ${fileType} with Math Mode (AI)... Processing pages with Gemini.`;
            } else {
                progressStatus.textContent = `Converting ${fileType} to Markdown...`;
            }

            const response = await fetch('/upload', {
                method: 'POST',
                body: formData,
            });

            progressBarFill.style.width = '80%';

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.error || 'Conversion failed');
            }

            progressBarFill.style.width = '100%';
            progressStatus.textContent = 'Complete!';

            // Short delay to show completion
            setTimeout(() => {
                showResult(data);
            }, 500);

        } catch (err) {
            progressStatus.textContent = 'Error!';
            progressBarFill.style.background = '#EF4444';
            showError(err.message);

            setTimeout(() => {
                resetUpload();
            }, 2000);
        }
    }

    // ─── Show Result ─────────────────────────────────────────────────────
    function showResult(data) {
        currentMarkdown = data.markdown;
        currentFilename = data.filename;

        // Update result card
        resultTitle.textContent = `Converted: ${data.filename}`;
        const engineLabel = data.engine === 'marker' ? ' (Math Mode)' : '';
        resultMeta.textContent = `${data.pages} ${data.file_type === 'PPTX' ? 'slide' : 'page'}${data.pages !== 1 ? 's' : ''} processed${engineLabel}`;

        // Raw markdown
        markdownRaw.textContent = data.markdown;

        // Rendered preview (simple markdown to HTML + KaTeX math)
        markdownPreview.innerHTML = renderMarkdown(data.markdown);

        // Render KaTeX math after inserting HTML
        renderMathInPreview();

        // Update stats
        if (data.stats) {
            statConversions.textContent = formatNumber(data.stats.total_conversions);
            statPages.textContent = formatNumber(data.stats.total_pages_processed);
        }

        // Hide upload, show result
        uploadCard.style.display = 'none';

        resultCard.classList.add('show');

        // Reset tabs to raw
        setActiveTab('raw');
    }

    // ─── Reset Upload ────────────────────────────────────────────────────
    function resetUpload() {
        uploadCard.style.display = 'block';
        resultCard.classList.remove('show');
        progressWrapper.classList.remove('show');
        progressBarFill.style.width = '0%';
        progressBarFill.style.background = '';
        fileInput.value = '';
    }

    // ─── Tab Switching ───────────────────────────────────────────────────
    tabBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            setActiveTab(btn.dataset.tab);
        });
    });

    function setActiveTab(tabName) {
        tabBtns.forEach(btn => {
            btn.classList.toggle('active', btn.dataset.tab === tabName);
        });
        tabPanels.forEach(panel => {
            panel.classList.toggle('active', panel.id === `panel-${tabName}`);
        });
    }

    // ─── Copy to Clipboard ───────────────────────────────────────────────
    btnCopy.addEventListener('click', async () => {
        try {
            await navigator.clipboard.writeText(currentMarkdown);
            btnCopy.classList.add('copied');
            const originalText = btnCopy.innerHTML;
            btnCopy.innerHTML = `
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                    <circle cx="9" cy="9" r="8" stroke="currentColor" stroke-width="1.5"/>
                    <path d="M6 9l2 2 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Copied!
            `;
            setTimeout(() => {
                btnCopy.classList.remove('copied');
                btnCopy.innerHTML = originalText;
            }, 2000);
        } catch (err) {
            showError('Failed to copy to clipboard');
        }
    });

    // ─── Download ────────────────────────────────────────────────────────
    btnDownload.addEventListener('click', () => {
        const blob = new Blob([currentMarkdown], { type: 'text/markdown' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = currentFilename;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    });

    // ─── New Conversion ──────────────────────────────────────────────────
    btnNew.addEventListener('click', resetUpload);

    // ─── Simple Markdown to HTML Renderer ────────────────────────────────
    function renderMarkdown(md) {
        // First, protect LaTeX math blocks from HTML escaping
        const mathBlocks = [];
        let processed = md;

        // Extract block math ($$...$$) — replace with placeholders
        processed = processed.replace(/\$\$([\s\S]*?)\$\$/g, (match, tex) => {
            const idx = mathBlocks.length;
            mathBlocks.push({ tex: tex.trim(), display: true });
            return `%%MATH_BLOCK_${idx}%%`;
        });

        // Extract inline math ($...$) — replace with placeholders
        // Avoid matching things like $5 or price $10
        processed = processed.replace(/(?<![\\$\w])\$([^\$\n]+?)\$(?![0-9])/g, (match, tex) => {
            const idx = mathBlocks.length;
            mathBlocks.push({ tex: tex.trim(), display: false });
            return `%%MATH_BLOCK_${idx}%%`;
        });

        // Now escape HTML
        let html = escapeHtml(processed);

        // Headings
        html = html.replace(/^### (.+)$/gm, '<h3>$1</h3>');
        html = html.replace(/^## (.+)$/gm, '<h2>$1</h2>');
        html = html.replace(/^# (.+)$/gm, '<h1>$1</h1>');

        // Bold + Italic
        html = html.replace(/\*\*\*(.+?)\*\*\*/g, '<strong><em>$1</em></strong>');
        html = html.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
        html = html.replace(/\*(.+?)\*/g, '<em>$1</em>');

        // Horizontal rule
        html = html.replace(/^---$/gm, '<hr>');

        // Unordered list items
        html = html.replace(/^- (.+)$/gm, '<li>$1</li>');
        // Wrap consecutive <li> in <ul>
        html = html.replace(/(<li>.*<\/li>\n?)+/g, (match) => `<ul>${match}</ul>`);

        // Paragraphs (wrap standalone lines)
        html = html.replace(/^(?!<[hluop]|<hr|%%MATH)(.+)$/gm, '<p>$1</p>');

        // Clean up empty paragraphs
        html = html.replace(/<p>\s*<\/p>/g, '');

        // Restore math blocks as KaTeX-ready spans/divs
        mathBlocks.forEach((block, idx) => {
            const placeholder = `%%MATH_BLOCK_${idx}%%`;
            const escapedPlaceholder = escapeHtml(placeholder);
            if (block.display) {
                const replacement = `<div class="math-block" data-math="${encodeURIComponent(block.tex)}" data-display="true"></div>`;
                html = html.replace(escapedPlaceholder, replacement);
                // Also handle if wrapped in <p> tags
                html = html.replace(`<p>${replacement}</p>`, replacement);
            } else {
                const replacement = `<span class="math-inline" data-math="${encodeURIComponent(block.tex)}" data-display="false"></span>`;
                html = html.replace(escapedPlaceholder, replacement);
            }
        });

        return html;
    }

    // ─── KaTeX Math Rendering ────────────────────────────────────────────
    function renderMathInPreview() {
        if (typeof katex === 'undefined') return;

        const mathElements = markdownPreview.querySelectorAll('[data-math]');
        mathElements.forEach(el => {
            const tex = decodeURIComponent(el.getAttribute('data-math'));
            const displayMode = el.getAttribute('data-display') === 'true';
            try {
                katex.render(tex, el, {
                    displayMode: displayMode,
                    throwOnError: false,
                    trust: true,
                    strict: false,
                });
            } catch (e) {
                // If KaTeX fails, show the raw LaTeX
                el.textContent = displayMode ? `$$${tex}$$` : `$${tex}$`;
                el.style.fontFamily = 'monospace';
                el.style.color = '#94A3B8';
            }
        });
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function formatNumber(num) {
        if (num >= 1000000) return (num / 1000000).toFixed(1) + 'M';
        if (num >= 1000) return (num / 1000).toFixed(1) + 'K';
        return num.toString();
    }

    // ─── Intersection Observer for Animations ────────────────────────────
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Observe feature cards and step cards
    document.querySelectorAll('.feature-card, .step-card, .stat-item').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(el);
    });

});
