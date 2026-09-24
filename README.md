# LegalEase – AI-Powered Legal Document Generator

> **A professional, full-featured legal technology web application built strictly with HTML5, CSS3, Vanilla JavaScript, PHP, and JSON.**

---

## 1. Project Title & Overview
**LegalEase** is an AI-powered legal document generation web application. It bridges artificial intelligence and legal drafting by transforming basic user inputs (contract type, participating parties, agreed terms, and effective dates) into authoritative, professionally formatted, binding-style legal agreements complete with recitals, numbered clauses, warranties, dispute resolutions, and execution signature blocks.

---

## 2. Project Creator Information
* **Developer:** **Vaishnavi G**
* **Institution:** **Government Arts College (Autonomous), Kumbakonam**
* **Course:** **B.Sc. Computer Science**
* **Academic Year:** **2nd Year**
* **Project Type:** Academic Project

---

## 3. Core Concept & Workflow
Drafting legal contracts typically requires expensive counsel or complex manual templating. **LegalEase** simplifies this into a 4-step workflow:

```
[ HOME PAGE / TEMPLATES ]
           ↓
[ DOCUMENT CREATION STUDIO ]
           ↓
[ ENTER PARTIES, TERMS & DATES ]
           ↓
[ VALIDATE & SEND TO PHP BACKEND ]
           ↓
    ┌──────────────────────┐
    │  GEMINI AI ACTIVATED │ (if API key present)
    │         -OR-         │
    │  JSON TEMPLATE ENGINE│ (graceful fallback)
    └──────────────────────┘
           ↓
[ GENERATED LEGAL DOCUMENT ]
           ↓
[ IN-BROWSER EDITING & REFINEMENT ]
           ↓
[ EXPORTS: PRINT/PDF | DOCX | HTML | TXT ]
```

---

## 4. Key Features

1. **AI-Powered Drafting via Google Gemini API:**
   - Server-side cURL integration using Google Gemini models (`gemini-1.5-flash`).
   - Crafts comprehensive legal recitals, definitions, detailed stipulations, and signature blocks.
2. **Autonomous Fallback / Demo Mode:**
   - Operates with 100% functionality out-of-the-box even before an API key is configured.
   - Built-in legal repository (`data/templates.json`) weaves user inputs into structured legal clauses without dummy filler text.
3. **Strict Zero-Trust API Security:**
   - **Never exposes the Gemini API key** to HTML, CSS, JavaScript, JSON, or browser network traffic.
   - All AI communications execute strictly on the PHP backend.
4. **Authentic Legal Document Canvas:**
   - Responsive parchment paper preview with formal legal typography (`Merriweather` serif).
   - Official seal, formatted parties banner, recitals (`WHEREAS`), structured clauses, and signature underlines.
5. **In-Place Live Document Editor:**
   - Directly edit clauses, party names, and terms on the preview canvas without page reloads.
   - One-click Save and Revert controls.
6. **Multi-Format Document Export:**
   - **Print / Save as PDF:** Optimized `@media print` stylesheet removing headers, buttons, and margins for native high-resolution PDF printing.
   - **Word (.docx):** Native Office Open XML file generator via PHP `ZipArchive`.
   - **Standalone HTML:** Complete self-contained HTML file with embedded typography and print styling.
   - **Plain Text (.txt):** Formatted clean ASCII text file.
   - **Copy to Clipboard:** One-click instant copy.
7. **Client-Side Document History (`localStorage`):**
   - Automatically saves up to 25 generated contracts in browser storage.
   - Drawer interface to view, reopen, delete, or clear history.
8. **8 Supported Contract Templates:**
   - Freelance Work Contract
   - Service Agreement
   - Business Agreement
   - Non-Disclosure Agreement (NDA)
   - Lease Agreement
   - Employment Offer Letter
   - Partnership Agreement
   - General Contract

---

## 5. Technology Stack & Multi-Tier Architecture
This project is built with a dual-tier architecture:
- **Web Frontend:** HTML5 (Semantic elements, ARIA labels, meta tags)
- **Styling:** CSS3 (Modern aurora gradient design system, print stylesheet, flexbox & responsive grid)
- **Client Logic:** Vanilla JavaScript (ES6+, `fetch()`, real-time visual field validation, `localStorage`, `contentEditable`)
- **Web Backend:** PHP 8.x (cURL REST communications, JSON parsing, ZipArchive for Word generation)
- **AI & NLP Module:** Python 3.9+ (`ai_engine/` - Contract clause analysis, NLP tokenization, dataset preprocessing, and legal risk classification)
- **Data Storage:** JSON (`data/templates.json`)

---

## 6. Directory Structure

```
LegalEase/
│
├── index.php                 # Modern SaaS landing page with templates & creator info
├── create.php                # Interactive generation workspace and live paper preview
├── generate.php              # Secure backend AJAX endpoint for AI & fallback generation
├── download.php              # Export handler for TXT, standalone HTML, and Word DOCX
├── config.php                # Active server configuration (API key & model settings)
├── config.example.php        # Example configuration template with setup instructions
├── README.md                 # Complete documentation and setup manual
│
├── ai_engine/                # Python Legal NLP & AI Contract Evaluation Module
│   ├── __init__.py           # Python package initializer
│   ├── config.py             # NLP hyperparameters, risk thresholds, and model configurations
│   ├── legal_nlp_analyzer.py # Legal clause analyzer, sentiment/risk evaluator, and jurisdiction scanner
│   ├── dataset_preprocessor.py# Raw legal agreement cleaner, tokenizer, and clause segmenter
│   ├── train_contract_model.py# Synthetic dataset trainer & TF-IDF/Naive Bayes classifier pipeline
│   ├── requirements.txt      # Python dependencies (spacy, scikit-learn, transformers, etc.)
│   └── README.md             # Dedicated Python module documentation and evaluation manual
│
├── css/
│   ├── style.css             # Core design system, legal paper canvas, and print CSS
│   └── responsive.css        # Responsive media queries for desktop, tablet, and mobile
│
├── js/
│   ├── app.js                # Mobile navigation, global toast notifications, UI helpers
│   ├── generator.js          # Form validation, fetch() AJAX, loading states, exports
│   ├── editor.js             # Live in-place canvas editor with Save/Cancel states
│   └── history.js            # Client-side localStorage document history and drawer
│
├── data/
│   └── templates.json        # Structured legal templates for 8 contract types
│
├── includes/
│   ├── header.php            # Universal responsive navbar and SEO meta headers
│   ├── footer.php            # Universal footer with student credentials and history drawer
│   ├── gemini.php            # Server-side PHP cURL integration for Google Gemini API
│   ├── template_generator.php# Dynamic fallback generator using templates.json
│   └── helpers.php           # Sanitization, validation, dates, and URL helpers
│
├── assets/
│   ├── logo.svg              # Scales of Justice emblem logo
│   └── icons/                # Clean SVG icons for all contract types
│       ├── briefcase.svg
│       ├── shield-check.svg
│       ├── lock.svg
│       ├── file-text.svg
│       ├── home.svg
│       ├── award.svg
│       ├── users.svg
│       └── file-check.svg
│
└── output/
    └── .gitkeep              # Placeholder for generated temporary file storage
```

---

## 7. Prerequisites & System Requirements
- **Web Server / PHP:** PHP 7.4 or PHP 8.0+ (PHP 8.2 recommended).
- **PHP Extensions:**
  - `curl` (required for Google Gemini AI calls)
  - `json` (required for data exchange)
  - `mbstring` (recommended for UTF-8 character encoding)
  - `zip` (`ZipArchive`, required for native `.docx` Microsoft Word exports)
- **Web Browser:** Any modern browser (Google Chrome, Microsoft Edge, Mozilla Firefox, Safari).

---

## 8. How to Run the Application

### Option 1 – Running via PHP Built-in Server (Fastest)

1. Open PowerShell, Command Prompt, or Terminal.
2. Navigate to the project directory:
   ```bash
   cd d:\NMvaishnavi
   ```
3. Start the PHP development server:
   ```bash
   php -S localhost:8000
   ```
4. Open your browser and navigate to:
   ```
   http://localhost:8000
   ```

---

### Option 2 – Running via XAMPP

1. Install **XAMPP** from [https://www.apachefriends.org/](https://www.apachefriends.org/).
2. Copy the entire `LegalEase` project folder into the XAMPP `htdocs` directory:
   ```
   C:\xampp\htdocs\LegalEase
   ```
3. Open the **XAMPP Control Panel** and click **Start** next to **Apache**.
4. Open your web browser and visit:
   ```
   http://localhost/LegalEase/
   ```

---

## 9. Configuring the Google Gemini API

LegalEase runs automatically in **Demo Mode** out of the box. To activate **AI Mode** with Google Gemini:

1. Obtain a free API key from **Google AI Studio**:
   - Visit: [https://aistudio.google.com/](https://aistudio.google.com/)
   - Sign in with your Google account and click **Get API Key**.
   - Create a key for **Gemini 1.5 Flash**.

2. Configure the key using either of these two methods:

   **Method A: Inside `config.php` (Recommended)**
   Open `config.php` in your editor and insert your API key:
   ```php
   define('GEMINI_API_KEY', 'AIzaSyYourActualKeyHere...');
   ```

   **Method B: Via System Environment Variable**
   Set an environment variable named `GEMINI_API_KEY`:
   ```powershell
   [System.Environment]::SetEnvironmentVariable('GEMINI_API_KEY', 'AIzaSyYourActualKeyHere...', 'User')
   ```

3. Refresh the web page. The header badge will immediately switch to:
   **`AI Mode Active`**.

---

## 10. Demo Mode vs. AI Mode

| Feature | AI Mode | Demo / Fallback Mode |
| :--- | :--- | :--- |
| **Engine** | Google Gemini 1.5 Flash | Built-in `data/templates.json` |
| **Requirements** | Active Internet + `GEMINI_API_KEY` | None (Works 100% Offline) |
| **Custom Terms** | AI expands and formalizes nuances | Numbered into structured legal clauses |
| **Document Quality** | Authoritative, dynamic legalese | Standard verified contract structure |
| **Failure Handling** | Falls back to template if offline | Zero downtime, instant output |

---

## 11. Troubleshooting & FAQs

* **Q: Why does the header say "Demo Mode Active"?**
  * **A:** No Gemini API key was found in `config.php` or environment variables. All features, document generation, editing, and exports still work using the built-in JSON template engine!
* **Q: Word DOCX download says "ZipArchive extension required"?**
  * **A:** Ensure the `zip` extension is enabled in your `php.ini` file (`extension=zip`). If unavailable, you can use **Print / Save as PDF**, **HTML**, or **TXT** exports.
* **Q: How do I save as PDF?**
  * **A:** Click **Print / PDF**. In your browser's print dialog, choose **"Save as PDF"** as the Destination and click Save. The print stylesheet automatically hides all website navigation, buttons, and borders.
* **Q: Where is my document history stored?**
  * **A:** In your browser's `localStorage`. No documents or confidential terms are stored on third-party servers.

---

## 12. Security & Compliance Notes
- **API Key Protection:** The Gemini API key is loaded only inside PHP server memory and never sent to client devices.
- **Sanitization:** All inputs undergo `strip_tags()` and `htmlspecialchars()` sanitization.
- **Legal Disclaimer:** Documents generated by LegalEase are intended for drafting and preliminary review purposes only and do not constitute formal legal counsel.

---

## 13. Academic Attribution
* **Project Title:** LegalEase – AI-Powered Legal Document Generator
* **Author:** Vaishnavi G
* **Degree:** B.Sc. Computer Science (2nd Year)
* **Institution:** Government Arts College (Autonomous), Kumbakonam
* **Academic Year:** 2025–2026
