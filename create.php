<?php
/**
 * LegalEase - Document Creation & Generation Workspace
 */

define('LEGALEASE_ACCESS', true);

require_once __DIR__ . '/includes/helpers.php';

$pageTitle = 'Create Legal Document - LegalEase';
$pageDesc = 'Generate, edit, preview, and download custom legal documents using AI or verified templates.';

// Load templates
$templatesData = loadTemplates();
$templatesList = $templatesData['templates'] ?? [];

// Check if a preselected template was passed via query parameter
$selectedType = trim($_GET['type'] ?? '');

$extraScripts = ['js/generator.js', 'js/editor.js'];

include __DIR__ . '/includes/header.php';
?>

<!-- Creation Workspace Header -->
<div class="create-page-header">
    <div class="container create-page-top">
        <div class="create-header-left">
            <a href="<?php echo $baseUrl; ?>/index.php" class="btn btn-back-home" title="Return to Home Page">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                <span>Back to Home</span>
            </a>
            <div class="create-titles-wrap">
                <h1 class="create-title">Legal Document Studio</h1>
                <p class="create-desc">Complete the essential terms below to draft an enforceable legal contract with AI assistance.</p>
            </div>
        </div>
        <div class="create-header-right">
            <a href="<?php echo $baseUrl; ?>/index.php" class="btn btn-outline-home-top" title="Return to Home Page">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                <span>Home Page</span>
            </a>
            <?php if ($isAiActive): ?>
                <span class="badge badge-blue" title="Requests routed through Google Gemini AI">
                    AI Mode &bull; Google Gemini
                </span>
            <?php else: ?>
                <span class="badge badge-gold" title="Using internal templates.json">
                    Demo Mode &bull; Built-in Templates
                </span>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Main Workspace Layout -->
<main class="container">
    <div class="workspace-layout">
        
        <!-- LEFT PANEL: GENERATION FORM -->
        <section class="form-panel" aria-labelledby="formHeading">
            <div class="panel-header">
                <h2 class="panel-title" id="formHeading">Document Details</h2>
                <button type="button" class="btn-fill-sample" id="btnFillSample" title="Populate form with realistic sample legal data">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v4"/><path d="m4.93 10.93 2.83-2.83"/><path d="M2 18h4"/><path d="M20 18h2"/><path d="m19.07 10.93-2.83-2.83"/><path d="M22 22H2"/><path d="m16 6-4 4-4-4"/></svg>
                    Fill Sample Data
                </button>
            </div>

            <form id="documentForm" novalidate>
                <!-- A. Document Type Dropdown -->
                <div class="form-group">
                    <div class="form-label-row">
                        <label for="docTypeSelect" class="form-label">
                            Document Type <span class="req">*</span>
                        </label>
                        <span id="docTypeCategoryBadge" class="field-meta-pill" style="display:none;"></span>
                    </div>
                    <div class="input-with-status">
                        <select id="docTypeSelect" name="document_type" class="form-control" required>
                            <option value="">-- Select Document Type --</option>
                            <?php foreach ($templatesList as $tmpl): ?>
                                <?php 
                                    $isSelected = ($selectedType === $tmpl['id'] || strcasecmp($selectedType, $tmpl['name']) === 0);
                                ?>
                                <option value="<?php echo htmlspecialchars($tmpl['name']); ?>" data-id="<?php echo htmlspecialchars($tmpl['id']); ?>" data-category="<?php echo htmlspecialchars($tmpl['category'] ?? 'General Legal'); ?>" <?php echo $isSelected ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($tmpl['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <span class="field-status-icon" id="status-icon-document_type"></span>
                    </div>
                    <div class="invalid-feedback" id="err-document_type">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <span>Please select a document type.</span>
                    </div>
                </div>

                <!-- B. Parties Involved -->
                <div class="form-group">
                    <div class="form-label-row">
                        <label for="partiesInput" class="form-label">
                            Parties Involved <span class="req">*</span>
                        </label>
                        <span id="partiesCountBadge" class="field-meta-pill" style="display:none;"></span>
                    </div>
                    <div class="input-with-status">
                        <textarea 
                            id="partiesInput" 
                            name="parties" 
                            class="form-control" 
                            rows="2" 
                            placeholder="Jane Doe (Service Provider), TechNova Inc. (Client)"
                            required></textarea>
                        <span class="field-status-icon" id="status-icon-parties"></span>
                    </div>
                    <div class="field-footer-info">
                        <span class="form-hint">List participating parties separated by a comma or 'and'.</span>
                    </div>
                    <div class="invalid-feedback" id="err-parties">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <span>Please specify the parties involved.</span>
                    </div>
                </div>

                <!-- C. Terms and Conditions -->
                <div class="form-group">
                    <div class="form-label-row">
                        <label for="termsInput" class="form-label">
                            Terms &amp; Conditions <span class="req">*</span>
                        </label>
                        <span id="termsCountBadge" class="field-meta-pill" style="display:none;"></span>
                    </div>
                    <div class="input-with-status">
                        <textarea 
                            id="termsInput" 
                            name="terms" 
                            class="form-control" 
                            rows="4" 
                            placeholder="Payment shall be made within 30 days;&#10;The provider shall complete the work within the agreed deadline;&#10;Confidentiality must be maintained;&#10;Either party may terminate with 15 days notice"
                            required></textarea>
                        <span class="field-status-icon" id="status-icon-terms"></span>
                    </div>
                    <div class="field-footer-info">
                        <span class="form-hint">Separate each clause with a semicolon (;) or new line.</span>
                    </div>
                    <div class="invalid-feedback" id="err-terms">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <span>Please provide the agreed terms and conditions.</span>
                    </div>
                </div>

                <!-- D. Effective Date -->
                <div class="form-group">
                    <div class="form-label-row">
                        <label for="effectiveDateInput" class="form-label">
                            Effective Date <span class="req">*</span>
                        </label>
                        <span id="dateStatusBadge" class="field-meta-pill" style="display:none;"></span>
                    </div>
                    <div class="input-with-status">
                        <input 
                            type="date" 
                            id="effectiveDateInput" 
                            name="effective_date" 
                            class="form-control" 
                            value="<?php echo date('Y-m-d'); ?>" 
                            required>
                        <span class="field-status-icon" id="status-icon-effective_date"></span>
                    </div>
                    <div class="invalid-feedback" id="err-effective_date">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <span>Please provide a valid effective date.</span>
                    </div>
                </div>

                <!-- E. Optional Additional Instructions -->
                <div class="form-group">
                    <label for="additionalNotesInput" class="form-label">
                        Optional Additional Instructions
                    </label>
                    <textarea 
                        id="additionalNotesInput" 
                        name="additional_notes" 
                        class="form-control" 
                        rows="2" 
                        placeholder="Any additional requirements for the document... (e.g. Include specific jurisdiction, non-solicitation period, etc.)"></textarea>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary btn-generate" id="btnGenerate">
                    <span class="btn-text">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/></svg>
                        Generate Legal Document
                    </span>
                    <span class="btn-spinner" style="display:none;">
                        <svg class="loading-spinner-inline" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="animation:spin 0.8s linear infinite;"><line x1="12" x2="12" y1="2" y2="6"/><line x1="12" x2="12" y1="18" y2="22"/><line x1="4.93" x2="7.76" y1="4.93" y2="7.76"/><line x1="16.24" x2="19.07" y1="16.24" y2="19.07"/><line x1="2" x2="6" y1="12" y2="12"/><line x1="18" x2="22" y1="12" y2="12"/><line x1="4.93" x2="7.76" y1="19.07" y2="16.24"/><line x1="16.24" x2="19.07" y1="7.76" y2="4.93"/></svg>
                        Generating Document...
                    </span>
                </button>
            </form>
        </section>

        <!-- RIGHT PANEL: LIVE PREVIEW & EXPORTS -->
        <section class="preview-panel" aria-label="Document Preview and Export">
            
            <!-- Toolbar -->
            <div class="preview-toolbar" id="previewToolbar">
                <div class="toolbar-status">
                    <a href="<?php echo $baseUrl; ?>/index.php" class="btn btn-secondary btn-sm btn-toolbar-home" title="Return to Home Page">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                        <span>Back to Home</span>
                    </a>
                    <span id="docStatusBadge" class="doc-status-badge">Awaiting Input</span>
                    <span id="docModeBadge" class="badge badge-blue" style="display:none;">AI Generated</span>
                </div>

                <div class="toolbar-actions" id="toolbarActions">
                    <!-- Edit Controls -->
                    <button type="button" class="btn btn-secondary btn-sm" id="btnEditDoc" disabled>
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                        Edit Document
                    </button>

                    <!-- Save & Cancel (Visible only during editing) -->
                    <button type="button" class="btn btn-success btn-sm" id="btnSaveEdit" style="display:none;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                        Save Changes
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm" id="btnCancelEdit" style="display:none;">
                        Cancel
                    </button>

                    <!-- Export Actions -->
                    <button type="button" class="btn btn-secondary btn-sm" id="btnPrintPdf" disabled title="Print or Save as PDF via native browser dialog">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
                        Print / PDF
                    </button>

                    <button type="button" class="btn btn-secondary btn-sm" id="btnDownloadDocx" disabled title="Download native Microsoft Word DOCX">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
                        DOCX
                    </button>

                    <button type="button" class="btn btn-secondary btn-sm" id="btnDownloadHtml" disabled title="Download standalone styled HTML document">
                        HTML
                    </button>

                    <button type="button" class="btn btn-secondary btn-sm" id="btnDownloadTxt" disabled title="Download plain text document">
                        TXT
                    </button>

                    <button type="button" class="btn btn-secondary btn-sm" id="btnCopyText" disabled title="Copy plain text to clipboard">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
                    </button>
                </div>
            </div>

            <!-- Post-Generation Action Card / Bar -->
            <div class="post-generation-bar" id="postGenerationBar" style="display:none;">
                <div class="post-gen-left">
                    <span class="post-gen-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    </span>
                    <div class="post-gen-info">
                        <strong>Document Ready!</strong>
                        <span>Print as PDF, download DOCX, or return to Home.</span>
                    </div>
                </div>
                <div class="post-gen-right">
                    <button type="button" class="btn btn-primary btn-sm" id="btnQuickPrint">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
                        Print / PDF
                    </button>
                    <a href="<?php echo $baseUrl; ?>/index.php" class="btn btn-secondary btn-sm btn-post-home">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                        Back to Home
                    </a>
                </div>
            </div>

            <!-- In-Place Editing Banner -->
            <div class="edit-mode-banner" id="editModeBanner">
                <span>
                    <strong>Editing Mode Active:</strong> You can directly modify any text, clause, or party name on the canvas below.
                </span>
                <span>Click "Save Changes" when finished.</span>
            </div>

            <!-- Canvas Paper Area -->
            <div class="document-canvas-wrap" id="canvasWrap">
                <article class="document-paper" id="documentPaper">
                    
                    <!-- Loading Overlay -->
                    <div class="preview-loading-overlay" id="loadingOverlay">
                        <div class="loading-spinner"></div>
                        <h4 class="loading-text">Generating your legal document...</h4>
                        <p class="loading-subtext">Synthesizing recitals, legal terms, obligations, and execution blocks.</p>
                    </div>

                    <!-- Empty State -->
                    <div class="preview-empty-state" id="emptyState">
                        <div class="empty-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><line x1="16" x2="8" y1="13" y2="13"/><line x1="16" x2="8" y1="17" y2="17"/><line x1="10" x2="8" y1="9" y2="9"/></svg>
                        </div>
                        <h3 class="empty-title">Ready to Generate</h3>
                        <p class="empty-desc">
                            Select a document type, list the parties and terms, and click <strong>Generate Legal Document</strong> to view the structured legal draft.
                        </p>
                    </div>

                    <!-- Live Render Container -->
                    <div id="documentContentArea" style="display:none;"></div>
                </article>
            </div>
        </section>

    </div>
</main>

<!-- Hidden Export Form for POST Download Triggers -->
<form id="exportForm" action="<?php echo $baseUrl; ?>/download.php" method="POST" style="display:none;" target="_blank">
    <input type="hidden" name="format" id="exportFormat" value="txt">
    <input type="hidden" name="title" id="exportTitle" value="LegalEase_Document">
    <input type="hidden" name="html_content" id="exportHtml" value="">
    <input type="hidden" name="text_content" id="exportText" value="">
</form>

<?php include __DIR__ . '/includes/footer.php'; ?>
