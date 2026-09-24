<?php
/**
 * LegalEase - JSON Template Fallback Generator
 * Generates professional, structured legal contracts using the user's input
 * and data/templates.json when AI is not configured or unavailable.
 */

if (!defined('LEGALEASE_ACCESS')) {
    define('LEGALEASE_ACCESS', true);
}

require_once __DIR__ . '/helpers.php';

/**
 * Generate a complete, formatted legal document from JSON templates and user inputs
 *
 * @param array $input Cleaned user input: document_type, parties, terms, effective_date, additional_notes
 * @return array Generated document data [title, html, text, mode, success]
 */
function generateDocumentFromTemplate($input) {
    $templatesData = loadTemplates();
    $templates = $templatesData['templates'] ?? [];

    $docType = $input['document_type'];
    $partiesRaw = $input['parties'];
    $termsRaw = $input['terms'];
    $effectiveDateRaw = $input['effective_date'];
    $additionalNotes = $input['additional_notes'] ?? '';

    // Find matching template
    $matchedTemplate = null;
    foreach ($templates as $tmpl) {
        if (strcasecmp($tmpl['name'], $docType) === 0 || strcasecmp($tmpl['id'], $docType) === 0) {
            $matchedTemplate = $tmpl;
            break;
        }
    }

    // Default template fallback if no exact match
    if (!$matchedTemplate) {
        $matchedTemplate = [
            'id' => 'general_contract',
            'name' => !empty($docType) ? $docType : 'General Contract',
            'category' => 'General Legal',
            'description' => 'Standard legal binding contract.',
            'recitals' => [
                'WHEREAS, the Parties desire to record their mutual understandings, representations, and undertakings in this binding instrument; and',
                'WHEREAS, for valuable consideration acknowledged by all signatories, the Parties covenant and agree as follows:'
            ],
            'sections' => [
                '1. Undertakings & Scope',
                '2. Terms & Consideration',
                '3. Mutual Responsibilities',
                '4. Term & Termination',
                '5. Governing Law'
            ]
        ];
    }

    $formattedDate = formatLegalDate($effectiveDateRaw);
    $docTitle = strtoupper($matchedTemplate['name']);
    $parsedTerms = parseTermsList($termsRaw);

    // If terms could not be parsed into multiple clauses, treat as one
    if (empty($parsedTerms)) {
        $parsedTerms = [htmlspecialchars($termsRaw, ENT_QUOTES | ENT_HTML5, 'UTF-8')];
    }

    // Split parties into two entities if separated by comma or semicolon
    $partiesList = preg_split('/[,;\n]+/', html_entity_decode($partiesRaw, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    $party1 = trim($partiesList[0] ?? 'Party of the First Part');
    $party2 = trim($partiesList[1] ?? 'Party of the Second Part');
    if (count($partiesList) > 2) {
        // If more than 2 entities, combine the rest into party2 or list them clearly
        $additionalParties = array_slice($partiesList, 2);
        $party2 .= ' (and ' . implode(', ', array_map('trim', $additionalParties)) . ')';
    }

    $party1Clean = htmlspecialchars($party1, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $party2Clean = htmlspecialchars($party2, ENT_QUOTES | ENT_HTML5, 'UTF-8');

    // Build document recitals
    $recitalsHtml = '';
    $recitalsText = '';
    if (!empty($matchedTemplate['recitals'])) {
        foreach ($matchedTemplate['recitals'] as $recital) {
            $recitalsHtml .= '<p class="doc-recital">' . htmlspecialchars($recital, ENT_QUOTES | ENT_HTML5, 'UTF-8') . '</p>';
            $recitalsText .= $recital . "\n\n";
        }
    }

    // Build clauses from user's actual terms
    $clausesHtml = '';
    $clausesText = '';
    $clauseCount = 1;

    // Define standard legal section headings based on document type
    $standardTitles = [
        '1. Scope of Engagement & Primary Obligations',
        '2. Performance Standards & Deliverables',
        '3. Compensation, Invoicing & Financial Settlement',
        '4. Operational Duties & Mutual Covenants',
        '5. Confidentiality, Trade Secrets & Non-Disclosure',
        '6. Term of Agreement & Termination Notice',
        '7. Warranties, Indemnification & Limitation of Liability',
        '8. Miscellaneous & Dispute Resolution'
    ];

    // Distribute user's terms across clauses
    foreach ($parsedTerms as $idx => $termItem) {
        $clauseHeading = $standardTitles[$idx] ?? ($clauseCount . '. Specific Agreed Condition');
        $clauseNum = $clauseCount;

        $clausesHtml .= '<div class="doc-clause">';
        $clausesHtml .= '<h3 class="clause-heading">' . htmlspecialchars($clauseHeading, ENT_QUOTES | ENT_HTML5, 'UTF-8') . '</h3>';
        $clausesHtml .= '<p class="clause-text"><strong>' . $clauseNum . '.1 Stipulation:</strong> ' . $termItem . '</p>';
        $clausesHtml .= '<p class="clause-text"><strong>' . $clauseNum . '.2 Compliance:</strong> Both Parties covenant and agree to adhere strictly to the above stipulation throughout the active duration of this Agreement.</p>';
        $clausesHtml .= '</div>';

        $clausesText .= "SECTION " . $clauseHeading . "\n";
        $clausesText .= $clauseNum . ".1 Stipulation: " . html_entity_decode($termItem, ENT_QUOTES | ENT_HTML5, 'UTF-8') . "\n";
        $clausesText .= $clauseNum . ".2 Compliance: Both Parties covenant and agree to adhere strictly to the above stipulation throughout the active duration of this Agreement.\n\n";

        $clauseCount++;
    }

    // Additional Notes / Custom instructions
    if (!empty($additionalNotes)) {
        $clausesHtml .= '<div class="doc-clause">';
        $clausesHtml .= '<h3 class="clause-heading">' . $clauseCount . '. Special Provisions & Custom Requirements</h3>';
        $clausesHtml .= '<p class="clause-text">The following express conditions have been specially agreed upon between the Parties:</p>';
        $clausesHtml .= '<blockquote class="clause-quote">' . nl2br($additionalNotes) . '</blockquote>';
        $clausesHtml .= '</div>';

        $clausesText .= "SECTION " . $clauseCount . ". Special Provisions & Custom Requirements\n";
        $clausesText .= "The following express conditions have been specially agreed upon between the Parties:\n";
        $clausesText .= html_entity_decode($additionalNotes, ENT_QUOTES | ENT_HTML5, 'UTF-8') . "\n\n";
        $clauseCount++;
    }

    // Standard Protective Clauses (Severability, Governing Law, Entire Agreement)
    $clausesHtml .= '<div class="doc-clause">';
    $clausesHtml .= '<h3 class="clause-heading">' . $clauseCount . '. Governing Law & General Provisions</h3>';
    $clausesHtml .= '<p class="clause-text"><strong>' . $clauseCount . '.1 Governing Law:</strong> This Agreement shall be governed by, construed, and enforced in accordance with the substantive laws of the agreed jurisdiction, without regard to conflict of laws principles.</p>';
    $clausesHtml .= '<p class="clause-text"><strong>' . $clauseCount . '.2 Severability:</strong> If any provision of this Agreement is held to be invalid or unenforceable, such provision shall be severed and the remaining provisions shall remain in full force and effect.</p>';
    $clausesHtml .= '<p class="clause-text"><strong>' . $clauseCount . '.3 Entire Agreement:</strong> This instrument constitutes the final, complete, and exclusive agreement between the Parties with respect to the subject matter hereof, superseding all prior oral or written communications.</p>';
    $clausesHtml .= '</div>';

    $clausesText .= "SECTION " . $clauseCount . ". Governing Law & General Provisions\n";
    $clausesText .= $clauseCount . ".1 Governing Law: This Agreement shall be governed by, construed, and enforced in accordance with the substantive laws of the agreed jurisdiction.\n";
    $clausesText .= $clauseCount . ".2 Severability: If any provision is held invalid, the remainder shall continue in full force.\n";
    $clausesText .= $clauseCount . ".3 Entire Agreement: This instrument constitutes the entire agreement between the Parties.\n\n";

    // Assemble Full HTML Document
    $html = <<<HTML
<div class="legal-document-content">
    <div class="doc-formal-header">
        <div class="doc-badge-seal">LEGAL INSTRUMENT</div>
        <h1 class="doc-main-title">{$docTitle}</h1>
        <div class="doc-meta-bar">
            <span><strong>Effective Date:</strong> {$formattedDate}</span>
            <span>&bull;</span>
            <span><strong>Status:</strong> Active & Binding</span>
        </div>
    </div>

    <div class="doc-section doc-parties-section">
        <h2 class="doc-section-title">PARTIES TO THIS AGREEMENT</h2>
        <p class="doc-intro-text">
            THIS AGREEMENT is made, entered into, and made effective as of <strong>{$formattedDate}</strong>, by and between the following named parties:
        </p>
        <div class="parties-grid">
            <div class="party-box">
                <span class="party-label">FIRST PARTY / PRINCIPAL:</span>
                <p class="party-name">{$party1Clean}</p>
            </div>
            <div class="party-divider">AND</div>
            <div class="party-box">
                <span class="party-label">SECOND PARTY / RECIPIENT:</span>
                <p class="party-name">{$party2Clean}</p>
            </div>
        </div>
        <p class="doc-intro-text text-muted">
            (The First Party and Second Party are collectively referred to herein as the "Parties" and individually as a "Party".)
        </p>
    </div>

    <div class="doc-section doc-recitals-section">
        <h2 class="doc-section-title">RECITALS &amp; BACKGROUND</h2>
        {$recitalsHtml}
        <p class="doc-covenant-lead">
            NOW, THEREFORE, in consideration of the mutual covenants and promises herein contained and other good and valuable consideration, the receipt and adequacy of which are hereby acknowledged, the Parties agree as follows:
        </p>
    </div>

    <div class="doc-section doc-clauses-section">
        <h2 class="doc-section-title">TERMS, COVENANTS &amp; CONDITIONS</h2>
        {$clausesHtml}
    </div>

    <div class="doc-section doc-signatures-section">
        <h2 class="doc-section-title">EXECUTION &amp; SIGNATURES</h2>
        <p class="doc-signature-intro">
            IN WITNESS WHEREOF, the Parties hereto have caused this <strong>{$docTitle}</strong> to be duly executed and delivered by their respective authorized representatives as of the Effective Date first written above.
        </p>
        
        <div class="signatures-grid">
            <div class="signature-block">
                <div class="sig-line"></div>
                <p class="sig-name"><strong>Party 1:</strong> {$party1Clean}</p>
                <p class="sig-role">Authorized Signatory / Representative</p>
                <p class="sig-date">Date: ________________________</p>
            </div>

            <div class="signature-block">
                <div class="sig-line"></div>
                <p class="sig-name"><strong>Party 2:</strong> {$party2Clean}</p>
                <p class="sig-role">Authorized Signatory / Representative</p>
                <p class="sig-date">Date: ________________________</p>
            </div>
        </div>
    </div>

    <div class="doc-formal-footer">
        <div class="footer-divider"></div>
        <p class="doc-watermark">LegalEase &bull; AI-Powered Legal Document Generator</p>
        <p class="doc-legal-disclaimer">
            <strong>NOTICE &amp; DISCLAIMER:</strong> This document is generated for informational, structural, and initial drafting purposes only and should not be construed as formal legal advice. Both parties should consult a qualified legal professional before executing any binding agreement.
        </p>
    </div>
</div>
HTML;

    // Plain text version
    $plainText = "=================================================================\n";
    $plainText .= "                        LEGAL INSTRUMENT                         \n";
    $plainText .= "             " . str_pad($docTitle, 52, " ", STR_PAD_BOTH) . "\n";
    $plainText .= "=================================================================\n\n";
    $plainText .= "EFFECTIVE DATE: " . $formattedDate . "\n\n";
    $plainText .= "PARTIES:\n";
    $plainText .= "1. First Party: " . $party1 . "\n";
    $plainText .= "2. Second Party: " . $party2 . "\n\n";
    $plainText .= "RECITALS:\n";
    $plainText .= $recitalsText;
    $plainText .= "NOW, THEREFORE, the Parties agree as follows:\n\n";
    $plainText .= "TERMS AND CONDITIONS:\n";
    $plainText .= "-----------------------------------------------------------------\n";
    $plainText .= $clausesText;
    $plainText .= "SIGNATURES:\n";
    $plainText .= "-----------------------------------------------------------------\n";
    $plainText .= "IN WITNESS WHEREOF, the Parties have executed this Agreement as of the Effective Date.\n\n";
    $plainText .= "FOR FIRST PARTY:\n";
    $plainText .= "Signature: _________________________________\n";
    $plainText .= "Name:      " . $party1 . "\n";
    $plainText .= "Date:      _________________________________\n\n";
    $plainText .= "FOR SECOND PARTY:\n";
    $plainText .= "Signature: _________________________________\n";
    $plainText .= "Name:      " . $party2 . "\n";
    $plainText .= "Date:      _________________________________\n\n";
    $plainText .= "=================================================================\n";
    $plainText .= "Generated via LegalEase - AI-Powered Legal Document Generator\n";
    $plainText .= "DISCLAIMER: For informational & drafting purposes only. Consult legal counsel before use.\n";
    $plainText .= "=================================================================\n";

    return [
        'success' => true,
        'mode' => 'template',
        'mode_label' => 'Built-in Template System',
        'notice' => 'Generated using LegalEase verified legal contract structure.',
        'document' => [
            'title' => $matchedTemplate['name'],
            'document_type' => $docType,
            'effective_date' => $effectiveDateRaw,
            'formatted_date' => $formattedDate,
            'parties' => $partiesRaw,
            'html' => $html,
            'text' => $plainText,
            'generated_at' => date('Y-m-d H:i:s')
        ]
    ];
}
