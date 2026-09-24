<?php
/**
 * LegalEase - Document Export & Download Handler
 * Supports TXT, HTML, and native DOCX (via Office Open XML / ZipArchive)
 */

if (!defined('LEGALEASE_ACCESS')) {
    define('LEGALEASE_ACCESS', true);
}

require_once __DIR__ . '/includes/helpers.php';

// Support both POST and GET
$format = strtolower(trim($_POST['format'] ?? $_GET['format'] ?? ''));
$title = trim($_POST['title'] ?? $_GET['title'] ?? 'Legal_Document');
$htmlContent = $_POST['html_content'] ?? '';
$textContent = $_POST['text_content'] ?? '';

// Sanitize filename
$safeTitle = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $title);
$safeTitle = trim($safeTitle, '_');
if (empty($safeTitle)) {
    $safeTitle = 'LegalEase_Document';
}
$filename = $safeTitle . '_' . date('Ymd_His');

// If checking DOCX support status via AJAX
if (isset($_GET['check_docx'])) {
    jsonResponse([
        'zip_available' => true,
        'native_zip' => class_exists('ZipArchive')
    ]);
}

if (empty($format)) {
    // If accessed directly without format, redirect to create page
    if (PHP_SAPI !== 'cli' && !isset($_GET['check_docx'])) {
        header('Location: create.php');
        exit;
    }
}

if (!empty($format)) {
    switch ($format) {
        case 'txt':
            header('Content-Type: text/plain; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '.txt"');
            header('Cache-Control: no-cache, no-store, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');
            
            $output = !empty($textContent) ? $textContent : strip_tags($htmlContent);
            echo $output;
            exit;

        case 'html':
            header('Content-Type: text/html; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '.html"');
            header('Cache-Control: no-cache, no-store, must-revalidate');
        
        // Wrap in complete standalone styled HTML with print styles
        $fullHtml = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$title} - LegalEase</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,300;0,400;0,700;1,300&family=Plus+Jakarta+Sans:wght@400;600;700&display=swap');
        body {
            font-family: 'Merriweather', Georgia, serif;
            background: #f1f5f9;
            color: #0f172a;
            line-height: 1.7;
            margin: 0;
            padding: 40px 20px;
        }
        .page-container {
            max-width: 850px;
            margin: 0 auto;
            background: #ffffff;
            padding: 60px 70px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
            border-radius: 4px;
        }
        .doc-formal-header { text-align: center; border-bottom: 2px solid #0f172a; padding-bottom: 20px; margin-bottom: 30px; }
        .doc-badge-seal { display: inline-block; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 11px; letter-spacing: 2px; font-weight: 700; color: #1e3a8a; border: 1px solid #1e3a8a; padding: 4px 14px; border-radius: 2px; margin-bottom: 12px; }
        .doc-main-title { font-size: 26px; font-weight: 700; margin: 0 0 10px 0; color: #0f172a; }
        .doc-meta-bar { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 13px; color: #64748b; }
        .doc-section { margin-bottom: 30px; }
        .doc-section-title { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 15px; letter-spacing: 1px; font-weight: 700; color: #1e293b; border-bottom: 1px solid #cbd5e1; padding-bottom: 6px; margin-bottom: 16px; }
        .parties-grid { display: flex; gap: 20px; margin: 20px 0; }
        .party-box { flex: 1; background: #f8fafc; border: 1px solid #e2e8f0; padding: 14px 18px; border-radius: 4px; }
        .party-label { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 11px; font-weight: 700; color: #64748b; display: block; margin-bottom: 4px; }
        .party-name { font-size: 15px; font-weight: 600; margin: 0; color: #0f172a; }
        .party-divider { display: flex; align-items: center; font-weight: bold; color: #94a3b8; font-family: 'Plus Jakarta Sans', sans-serif; }
        .doc-recital { font-style: italic; margin-bottom: 12px; color: #334155; }
        .doc-clause { margin-bottom: 20px; }
        .clause-heading { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 16px; font-weight: 700; color: #0f172a; margin: 0 0 8px 0; }
        .clause-text { margin: 0 0 8px 0; font-size: 15px; text-align: justify; }
        .signatures-grid { display: flex; justify-content: space-between; gap: 40px; margin-top: 40px; }
        .signature-block { flex: 1; }
        .sig-line { border-bottom: 1px solid #0f172a; height: 50px; margin-bottom: 10px; }
        .sig-name { font-size: 14px; margin: 0; }
        .sig-role, .sig-date { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 12px; color: #64748b; margin: 2px 0; }
        .doc-formal-footer { margin-top: 50px; padding-top: 20px; border-top: 1px solid #cbd5e1; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 11px; color: #94a3b8; text-align: center; }
        @media print {
            body { background: #ffffff; padding: 0; }
            .page-container { border: none; box-shadow: none; padding: 0; max-width: 100%; }
        }
    </style>
</head>
<body>
    <div class="page-container">
        {$htmlContent}
    </div>
</body>
</html>
HTML;
        echo $fullHtml;
        exit;

    case 'docx':
        // If PHP ZipArchive extension is available, generate native DOCX
        if (class_exists('ZipArchive')) {
            $docxData = generateDocxBinary($title, $textContent, $htmlContent);
            
            header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
            header('Content-Disposition: attachment; filename="' . $filename . '.docx"');
            header('Content-Length: ' . strlen($docxData));
            header('Cache-Control: no-cache, no-store, must-revalidate');
            echo $docxData;
            exit;
        }

        // Graceful universal fallback: Word-compatible format (.doc) that opens natively in Microsoft Word
        header('Content-Type: application/msword; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '.doc"');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        $wordDocHtml = <<<WORDHTML
<html xmlns:o="urn:schemas-microsoft-com:office:office"
      xmlns:w="urn:schemas-microsoft-com:office:word"
      xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta charset="utf-8">
    <title>{$title}</title>
    <style>
        body { font-family: 'Times New Roman', serif; font-size: 12pt; line-height: 1.5; color: #000000; margin: 1in; }
        h1 { font-family: 'Arial', sans-serif; font-size: 18pt; text-align: center; color: #0f172a; margin-bottom: 12pt; }
        h2 { font-family: 'Arial', sans-serif; font-size: 14pt; color: #1e293b; margin-top: 14pt; }
        h3 { font-family: 'Arial', sans-serif; font-size: 12pt; color: #0f172a; margin-top: 10pt; }
        p { margin-bottom: 8pt; text-align: justify; }
        .doc-formal-header { text-align: center; border-bottom: 2pt solid #0f172a; padding-bottom: 12pt; margin-bottom: 18pt; }
        .doc-badge-seal { font-family: 'Arial', sans-serif; font-size: 9pt; font-weight: bold; border: 1pt solid #1e3a8a; padding: 2pt 8pt; display: inline-block; margin-bottom: 8pt; }
        .doc-meta-bar { font-family: 'Arial', sans-serif; font-size: 10pt; color: #555555; }
        .party-box { border: 1pt solid #cccccc; padding: 8pt 12pt; margin-bottom: 8pt; background-color: #f8fafc; }
        .party-label { font-family: 'Arial', sans-serif; font-weight: bold; font-size: 9pt; color: #555555; }
        .party-name { font-weight: bold; }
        .doc-clause { margin-bottom: 14pt; }
        .clause-heading { font-family: 'Arial', sans-serif; font-weight: bold; font-size: 11pt; }
        .signatures-grid { margin-top: 24pt; }
        .signature-block { width: 45%; display: inline-block; vertical-align: top; margin-top: 16pt; }
        .sig-line { border-bottom: 1pt solid #000000; height: 30pt; margin-bottom: 6pt; }
        .doc-formal-footer { margin-top: 30pt; border-top: 1pt solid #cccccc; padding-top: 10pt; font-family: 'Arial', sans-serif; font-size: 9pt; color: #777777; text-align: center; }
    </style>
</head>
<body>
    {$htmlContent}
</body>
</html>
WORDHTML;
        echo $wordDocHtml;
        exit;

    default:
        http_response_code(400);
        echo 'Unsupported export format.';
        exit;
    }
}

/**
 * Generate native Word (.docx) file binary using ZipArchive and Office Open XML
 */
function generateDocxBinary($title, $text, $html) {
    $tempFile = tempnam(sys_get_temp_dir(), 'doc_');
    $zip = new ZipArchive();
    
    if ($zip->open($tempFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        throw new Exception('Could not create temporary DOCX archive.');
    }

    // 1. [Content_Types].xml
    $contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
        . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
        . '<Default Extension="xml" ContentType="application/xml"/>'
        . '<Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>'
        . '<Override PartName="/word/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.styles+xml"/>'
        . '</Types>';
    $zip->addFromString('[Content_Types].xml', $contentTypes);

    // 2. _rels/.rels
    $rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
        . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>'
        . '</Relationships>';
    $zip->addFromString('_rels/.rels', $rels);

    // 3. word/_rels/document.xml.rels
    $docRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
        . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
        . '</Relationships>';
    $zip->addFromString('word/_rels/document.xml.rels', $docRels);

    // 4. word/styles.xml
    $stylesXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<w:styles xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">'
        . '<w:docDefaults>'
        . '<w:rPrDefault><w:rPr><w:rFonts w:ascii="Calibri" w:hAnsi="Calibri"/><w:sz w:val="22"/><w:szCs w:val="22"/></w:rPr></w:rPrDefault>'
        . '</w:docDefaults>'
        . '<w:style w:type="paragraph" w:styleId="Title">'
        . '<w:name w:val="Title"/><w:rPr><w:b/><w:sz w:val="36"/><w:szCs w:val="36"/><w:color w:val="1E293B"/></w:rPr>'
        . '</w:style>'
        . '<w:style w:type="paragraph" w:styleId="Heading1">'
        . '<w:name w:val="heading 1"/><w:rPr><w:b/><w:sz w:val="28"/><w:szCs w:val="28"/><w:color w:val="0F172A"/></w:rPr>'
        . '</w:style>'
        . '</w:styles>';
    $zip->addFromString('word/styles.xml', $stylesXml);

    // 5. word/document.xml
    // Convert lines of text to Word paragraphs
    $sourceText = !empty($text) ? $text : strip_tags($html);
    $lines = explode("\n", str_replace(["\r\n", "\r"], "\n", $sourceText));
    
    $paragraphsXml = '';
    foreach ($lines as $line) {
        $trimmed = trim($line);
        if ($trimmed === '') {
            $paragraphsXml .= '<w:p><w:pPr><w:spacing w:after="120"/></w:pPr></w:p>';
            continue;
        }

        $xmlSafe = htmlspecialchars($trimmed, ENT_XML1 | ENT_COMPAT, 'UTF-8');
        
        // Check if line looks like a title or section heading
        if (preg_match('/^(===|SECTION|\d+\.|\bPARTIES\b|\bRECITALS\b|\bTERMS\b|\bSIGNATURES\b)/i', $trimmed)) {
            $paragraphsXml .= '<w:p>'
                . '<w:pPr><w:spacing w:before="240" w:after="120"/><w:jc w:val="left"/></w:pPr>'
                . '<w:r><w:rPr><w:b/><w:sz w:val="26"/><w:color w:val="0F172A"/></w:rPr>'
                . '<w:t>' . $xmlSafe . '</w:t></w:r>'
                . '</w:p>';
        } else {
            $paragraphsXml .= '<w:p>'
                . '<w:pPr><w:spacing w:after="120"/><w:jc w:val="both"/></w:pPr>'
                . '<w:r><w:rPr><w:sz w:val="22"/><w:color w:val="1E293B"/></w:rPr>'
                . '<w:t xml:space="preserve">' . $xmlSafe . '</w:t></w:r>'
                . '</w:p>';
        }
    }

    $docXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">'
        . '<w:body>'
        . '<w:p><w:pPr><w:jc w:val="center"/><w:spacing w:after="200"/></w:pPr>'
        . '<w:r><w:rPr><w:b/><w:sz w:val="32"/><w:color w:val="1E3A8A"/></w:rPr>'
        . '<w:t>' . htmlspecialchars($title, ENT_XML1, 'UTF-8') . '</w:t></w:r>'
        . '</w:p>'
        . $paragraphsXml
        . '<w:sectPr><w:pgSz w:w="12240" w:h="15840"/><w:pgMar w:top="1440" w:right="1440" w:bottom="1440" w:left="1440"/></w:sectPr>'
        . '</w:body>'
        . '</w:document>';

    $zip->addFromString('word/document.xml', $docXml);
    $zip->close();

    $binary = file_get_contents($tempFile);
    @unlink($tempFile);
    return $binary;
}
