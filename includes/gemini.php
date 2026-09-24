<?php
/**
 * LegalEase - Gemini AI Integration via PHP cURL
 * Secure server-side interface to Google Gemini API
 */

if (!defined('LEGALEASE_ACCESS')) {
    define('LEGALEASE_ACCESS', true);
}

require_once __DIR__ . '/helpers.php';

/**
 * Call Google Gemini API to draft a structured legal document
 *
 * @param array $input Cleaned input array: document_type, parties, terms, effective_date, additional_notes
 * @return array [bool success, string mode, array document, string error]
 */
function generateDocumentWithGemini($input) {
    if (!isGeminiConfigured()) {
        return [
            'success' => false,
            'error' => 'API_KEY_NOT_CONFIGURED',
            'message' => 'Gemini API key is not configured.'
        ];
    }

    $docType = $input['document_type'];
    $parties = $input['parties'];
    $terms = $input['terms'];
    $effectiveDate = $input['effective_date'];
    $formattedDate = formatLegalDate($effectiveDate);
    $additionalNotes = !empty($input['additional_notes']) ? $input['additional_notes'] : 'None provided.';

    // Construct prompt
    $prompt = <<<PROMPT
You are a senior legal drafting attorney and document specialist.
Draft an authoritative, complete, comprehensive, and professionally structured legal document based strictly on the following specifications:

DOCUMENT TYPE: {$docType}
PARTIES INVOLVED: {$parties}
EFFECTIVE DATE: {$formattedDate} (Raw: {$effectiveDate})
AGREED TERMS & CONDITIONS (Delimited by semicolons or lines):
{$terms}

ADDITIONAL USER INSTRUCTIONS:
{$additionalNotes}

DRAFTING INSTRUCTIONS & OUTPUT REQUIREMENTS:
1. Draft the full, binding legal document without placeholders or ellipses. Integrate ALL the user's terms into detailed, enforceable, numbered legal clauses.
2. Return ONLY the HTML code wrapped inside `<div class="legal-document-content">` and ending with `</div>`. Do NOT include markdown code blocks like ```html or ```.
3. Structure the document with this exact clean semantic hierarchy:
   - `<div class="doc-formal-header">`: Includes `<div class="doc-badge-seal">LEGAL INSTRUMENT</div>`, `<h1 class="doc-main-title">{$docType}</h1>`, and `<div class="doc-meta-bar"><span><strong>Effective Date:</strong> {$formattedDate}</span><span>&bull;</span><span><strong>Status:</strong> Active & Binding</span></div>`.
   - `<div class="doc-section doc-parties-section">`: Section title "PARTIES TO THIS AGREEMENT", opening paragraph detailing the parties and date, and `<div class="parties-grid">` with `<div class="party-box"><span class="party-label">FIRST PARTY:</span><p class="party-name">...</p></div>` and `<div class="party-box"><span class="party-label">SECOND PARTY:</span><p class="party-name">...</p></div>`.
   - `<div class="doc-section doc-recitals-section">`: Section title "RECITALS & BACKGROUND", with standard WHEREAS recitals explaining background and intent, concluding with "NOW, THEREFORE...".
   - `<div class="doc-section doc-clauses-section">`: Section title "TERMS, COVENANTS & CONDITIONS", containing numbered clauses in `<div class="doc-clause"><h3 class="clause-heading">1. Scope of Engagement...</h3><p class="clause-text">...</p></div>`. Clauses MUST incorporate:
     - 1. Scope, Deliverables & Purpose
     - 2. Obligations and Performance
     - 3. Compensation, Payment Terms & Invoicing (if applicable)
     - 4. Confidentiality & Non-Disclosure
     - 5. Intellectual Property Rights (if applicable)
     - 6. Warranties & Limitation of Liability
     - 7. Term and Termination
     - 8. Governing Law, Severability & Dispute Resolution
   - `<div class="doc-section doc-signatures-section">`: Section title "EXECUTION & SIGNATURES", with intro "IN WITNESS WHEREOF...", and `<div class="signatures-grid">` containing two `<div class="signature-block"><div class="sig-line"></div><p class="sig-name">...</p><p class="sig-role">Authorized Signatory</p><p class="sig-date">Date: ________________________</p></div>`.
   - `<div class="doc-formal-footer">`: Includes `<div class="footer-divider"></div>`, `<p class="doc-watermark">LegalEase &bull; AI-Powered Legal Document Generator</p>`, and `<p class="doc-legal-disclaimer"><strong>DISCLAIMER:</strong> This document is AI-generated for informational and drafting purposes and should be reviewed by a qualified legal professional before use.</p>`.

4. Use clear, formal, modern legal English. Do not add markdown commentary outside the HTML.
PROMPT;

    $endpoint = rtrim(GEMINI_API_ENDPOINT, '/') . '?key=' . urlencode(GEMINI_API_KEY);

    $payload = [
        'contents' => [
            [
                'role' => 'user',
                'parts' => [
                    ['text' => $prompt]
                ]
            ]
        ],
        'generationConfig' => [
            'temperature' => 0.2,
            'topP' => 0.85,
            'maxOutputTokens' => 4096
        ],
        'safetySettings' => [
            [
                'category' => 'HARM_CATEGORY_HARASSMENT',
                'threshold' => 'BLOCK_NONE'
            ],
            [
                'category' => 'HARM_CATEGORY_HATE_SPEECH',
                'threshold' => 'BLOCK_NONE'
            ],
            [
                'category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT',
                'threshold' => 'BLOCK_NONE'
            ],
            [
                'category' => 'HARM_CATEGORY_DANGEROUS_CONTENT',
                'threshold' => 'BLOCK_NONE'
            ]
        ]
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $endpoint);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'User-Agent: LegalEase-DocGen/1.0'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, defined('API_TIMEOUT') ? API_TIMEOUT : 30);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    // On local Windows dev servers without SSL cert bundle, prevent fatal cURL SSL error
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

    $response = curl_exec($ch);
    $curlError = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response === false || !empty($curlError)) {
        return [
            'success' => false,
            'error' => 'CURL_REQUEST_FAILED',
            'message' => 'Network error connecting to Gemini AI service.'
        ];
    }

    if ($httpCode !== 200) {
        $errorMsg = 'Gemini API returned status code ' . $httpCode;
        $decodedErr = json_decode($response, true);
        if (isset($decodedErr['error']['message'])) {
            // Strip any accidental key leakage in error text
            $cleanMsg = str_replace(GEMINI_API_KEY, '[REDACTED]', $decodedErr['error']['message']);
            $errorMsg .= ': ' . $cleanMsg;
        }
        return [
            'success' => false,
            'error' => 'API_HTTP_ERROR_' . $httpCode,
            'message' => $errorMsg
        ];
    }

    $responseData = json_decode($response, true);
    if (!is_array($responseData)) {
        return [
            'success' => false,
            'error' => 'INVALID_JSON_RESPONSE',
            'message' => 'Malformed response from AI provider.'
        ];
    }

    // Extract text from Gemini structure
    $generatedText = '';
    if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
        $generatedText = $responseData['candidates'][0]['content']['parts'][0]['text'];
    }

    if (empty(trim($generatedText))) {
        return [
            'success' => false,
            'error' => 'EMPTY_RESPONSE',
            'message' => 'AI returned an empty document.'
        ];
    }

    // Clean up any markdown code block wrappers (e.g. ```html ... ```)
    $cleanHtml = trim($generatedText);
    if (preg_match('/^```(?:html)?\s*(.*?)\s*```$/is', $cleanHtml, $matches)) {
        $cleanHtml = trim($matches[1]);
    }

    // Ensure it is enclosed in legal-document-content container if missing
    if (strpos($cleanHtml, 'legal-document-content') === false) {
        $cleanHtml = '<div class="legal-document-content">' . $cleanHtml . '</div>';
    }

    // Generate plain text version from HTML
    $plainText = convertHtmlToLegalText($cleanHtml, $docType, $formattedDate);

    return [
        'success' => true,
        'mode' => 'ai',
        'mode_label' => 'Google Gemini AI',
        'notice' => 'Generated dynamically using Google Gemini AI legal drafting engine.',
        'document' => [
            'title' => $docType,
            'document_type' => $docType,
            'effective_date' => $effectiveDate,
            'formatted_date' => $formattedDate,
            'parties' => $parties,
            'html' => $cleanHtml,
            'text' => $plainText,
            'generated_at' => date('Y-m-d H:i:s')
        ]
    ];
}

/**
 * Utility to convert generated legal HTML to readable plain text
 */
function convertHtmlToLegalText($html, $docType, $date) {
    // Replace headings with uppercase text and underlines
    $text = preg_replace('/<h[12][^>]*>(.*?)<\/h[12]>/i', "\n\n=== $1 ===\n", $html);
    $text = preg_replace('/<h3[^>]*>(.*?)<\/h3>/i', "\n\n-- $1 --\n", $text);
    $text = preg_replace('/<p[^>]*>/i', "\n", $text);
    $text = preg_replace('/<\/p>/i', "\n", $text);
    $text = preg_replace('/<br\s*\/?>/i', "\n", $text);
    $text = strip_tags($text);
    $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    // Normalize redundant newlines
    $text = preg_replace("/\n{3,}/", "\n\n", $text);
    return trim($text);
}
