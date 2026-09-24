<?php
/**
 * LegalEase - Landing Page
 */

define('LEGALEASE_ACCESS', true);

require_once __DIR__ . '/includes/helpers.php';

$pageTitle = 'LegalEase – AI-Powered Legal Document Generator';
$pageDesc = 'Create structured, authoritative legal agreements and contracts in seconds with AI and smart legal templates.';

// Load templates for dynamic template cards
$templatesData = loadTemplates();
$templatesList = $templatesData['templates'] ?? [];

include __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container hero-content">
        <div class="hero-pill">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/></svg>
            Next-Gen AI Legal Technology
        </div>

        <h1 class="hero-title">Legal<span>Ease</span></h1>
        <p class="hero-subtitle">AI-Powered Legal Document Generator</p>
        <p class="hero-description">
            Create structured legal documents quickly using AI-powered document generation. Turn simple terms into enforceable, professionally formatted agreements with smart clauses, recitals, and signature blocks.
        </p>

        <div class="hero-actions">
            <a href="<?php echo $baseUrl; ?>/create.php" class="btn btn-primary btn-lg">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                Create Document
            </a>
            <a href="#templates" class="btn btn-secondary btn-lg">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
                Explore Templates
            </a>
        </div>

        <div class="hero-stats">
            <div class="stat-item">
                <div class="stat-num">8+</div>
                <div class="stat-label">Legal Contract Types</div>
            </div>
            <div class="stat-item">
                <div class="stat-num">&lt; 3s</div>
                <div class="stat-label">Instant Generation</div>
            </div>
            <div class="stat-item">
                <div class="stat-num">4 Formats</div>
                <div class="stat-label">PDF, DOCX, HTML, TXT</div>
            </div>
            <div class="stat-item">
                <div class="stat-num">100%</div>
                <div class="stat-label">Server-Side Privacy</div>
            </div>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section class="section section-alt" id="how-it-works">
    <div class="container">
        <div class="section-header">
            <span class="section-tag">Simplified Process</span>
            <h2 class="section-title">How It Works</h2>
            <p class="section-desc">Generate ready-to-sign legal instruments in four straightforward steps without complex legalese hurdles.</p>
        </div>

        <div class="steps-grid">
            <div class="step-card">
                <div class="step-badge">01</div>
                <div class="step-icon-wrap">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="16" height="20" x="4" y="2" rx="2"/><path d="M8 6h8"/><path d="M8 10h8"/><path d="M8 14h6"/></svg>
                </div>
                <h3 class="step-title">Choose Document Type</h3>
                <p class="step-desc">Select from our vetted library of agreements including NDAs, Freelance Contracts, Leases, and Service Agreements.</p>
            </div>

            <div class="step-card">
                <div class="step-badge">02</div>
                <div class="step-icon-wrap">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>
                </div>
                <h3 class="step-title">Enter Parties &amp; Terms</h3>
                <p class="step-desc">Provide party identities, effective date, and outline agreed conditions simply separated by semicolons.</p>
            </div>

            <div class="step-card">
                <div class="step-badge">03</div>
                <div class="step-icon-wrap">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/></svg>
                </div>
                <h3 class="step-title">Generate with AI</h3>
                <p class="step-desc">Gemini AI synthesizes your inputs into structured, professional legal clauses, recitals, and warranties.</p>
            </div>

            <div class="step-card">
                <div class="step-badge">04</div>
                <div class="step-icon-wrap">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                </div>
                <h3 class="step-title">Edit &amp; Download</h3>
                <p class="step-desc">Fine-tune in the built-in browser editor, then export directly to Print/PDF, Word DOCX, HTML, or TXT.</p>
            </div>
        </div>
    </div>
</section>

<!-- Templates Catalog Section -->
<section class="section" id="templates">
    <div class="container">
        <div class="section-header">
            <span class="section-tag">Document Catalog</span>
            <h2 class="section-title">Explore Legal Templates</h2>
            <p class="section-desc">Select a document template below to start drafting immediately with recommended terms and structure.</p>
        </div>

        <div class="templates-grid">
            <?php foreach ($templatesList as $tmpl): ?>
                <div class="template-card">
                    <div class="template-card-top">
                        <div class="template-icon-bar">
                            <div class="template-icon">
                                <img src="<?php echo $baseUrl; ?>/assets/icons/<?php echo htmlspecialchars($tmpl['icon']); ?>.svg" alt="<?php echo htmlspecialchars($tmpl['name']); ?>" width="22" height="22">
                            </div>
                            <span class="template-category"><?php echo htmlspecialchars($tmpl['category']); ?></span>
                        </div>
                        <h3 class="template-name"><?php echo htmlspecialchars($tmpl['name']); ?></h3>
                        <p class="template-desc"><?php echo htmlspecialchars($tmpl['description']); ?></p>
                    </div>
                    <div>
                        <a href="<?php echo $baseUrl; ?>/create.php?type=<?php echo urlencode($tmpl['id']); ?>" class="btn btn-secondary btn-sm" style="width:100%;">
                            Use Template
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Key Platform Features -->
<section class="section section-alt">
    <div class="container">
        <div class="section-header">
            <span class="section-tag">Enterprise Architecture</span>
            <h2 class="section-title">Engineered for Precision &amp; Security</h2>
            <p class="section-desc">Built strictly with vanilla web standards and secure server-side PHP architecture.</p>
        </div>

        <div class="steps-grid">
            <div class="step-card">
                <div class="step-icon-wrap" style="color:var(--primary);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                </div>
                <h3 class="step-title">Strict API Security</h3>
                <p class="step-desc">Your Gemini API key never reaches the browser or client-side code. All AI requests execute securely via server-side PHP cURL.</p>
            </div>

            <div class="step-card">
                <div class="step-icon-wrap" style="color:var(--gold);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                </div>
                <h3 class="step-title">Autonomous Fallback</h3>
                <p class="step-desc">Zero downtime. If Gemini API is unconfigured or rate-limited, LegalEase instantly switches to the structured JSON template engine.</p>
            </div>

            <div class="step-card">
                <div class="step-icon-wrap" style="color:var(--emerald);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                </div>
                <h3 class="step-title">In-Place Document Editor</h3>
                <p class="step-desc">Adjust any clause, fix typos, or add custom provisions directly in the document canvas with live preview and save capabilities.</p>
            </div>

            <div class="step-card">
                <div class="step-icon-wrap" style="color:var(--navy-900);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 22h14a2 2 0 0 0 2-2V7.5L14.5 2H6a2 2 0 0 0-2 2v4"/><polyline points="14 2 14 8 20 8"/><path d="M3 15h6"/><path d="M6 12v6"/></svg>
                </div>
                <h3 class="step-title">Multi-Format Exports</h3>
                <p class="step-desc">Download clean text files, formatted HTML packages, native Microsoft Word DOCX, or trigger browser-native print to PDF.</p>
            </div>
        </div>
    </div>
</section>

<!-- About Section & Student Project Information -->
<section class="section" id="about">
    <div class="container">
        <div class="about-grid">
            <!-- Project Developer Card -->
            <div class="about-card">
                <span class="creator-tag">Academic Project</span>
                <h3 class="creator-name">Vaishnavi G</h3>
                <p class="creator-role">Lead Developer &bull; B.Sc. Computer Science</p>

                <ul class="creator-details">
                    <li>
                        <span>Academic Degree:</span>
                        <strong>B.Sc. Computer Science</strong>
                    </li>
                    <li>
                        <span>Academic Year:</span>
                        <strong>2nd Year</strong>
                    </li>
                    <li>
                        <span>Institution:</span>
                        <strong>Government Arts College (Autonomous)</strong>
                    </li>
                    <li>
                        <span>Location:</span>
                        <strong>Kumbakonam, Tamil Nadu</strong>
                    </li>
                    <li>
                        <span>Tech Stack:</span>
                        <strong>PHP, Python (AI NLP Engine), Vanilla JS, CSS3, JSON</strong>
                    </li>
                </ul>

                <a href="<?php echo $baseUrl; ?>/create.php" class="btn btn-primary btn-sm">
                    Test Legal Document Generator
                </a>
            </div>

            <!-- About Text -->
            <div class="about-text">
                <span class="section-tag">Project Overview</span>
                <h3>Bridging Law &amp; Artificial Intelligence</h3>
                <p>
                    <strong>LegalEase</strong> is an AI-powered legal document generation system designed to simplify the initial drafting process for common legal documents. By transforming plain terms into legally compliant instruments, it eliminates drafting friction for small businesses, freelancers, and individuals.
                </p>
                <p>
                    This application was conceived, designed, and developed by <strong>Vaishnavi G</strong> as a capstone project for the 2nd Year <strong>B.Sc. Computer Science</strong> program at <strong>Government Arts College (Autonomous), Kumbakonam</strong>.
                </p>

                <ul class="features-check-list">
                    <li>
                        <span class="check-icon">&#10003;</span>
                        Vanilla Architecture (Zero Node/React)
                    </li>
                    <li>
                        <span class="check-icon">&#10003;</span>
                        Google Gemini AI Integration
                    </li>
                    <li>
                        <span class="check-icon">&#10003;</span>
                        Autonomous JSON Fallback Engine
                    </li>
                    <li>
                        <span class="check-icon">&#10003;</span>
                        Client-Side localStorage Document History
                    </li>
                    <li>
                        <span class="check-icon">&#10003;</span>
                        Office Open XML Word (.docx) Generation
                    </li>
                    <li>
                        <span class="check-icon">&#10003;</span>
                        Fully Responsive &amp; Print-Ready
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Contact & Information Section -->
<section class="section section-alt" id="contact">
    <div class="container" style="max-width: 760px; text-align: center;">
        <span class="section-tag">Project Contact</span>
        <h2 class="section-title">LegalEase Project Information</h2>
        <p class="section-desc" style="margin-bottom: 30px;">
            For academic verification and technical demonstrations of this B.Sc. Computer Science project:
        </p>

        <div style="background-color: var(--white); border: 1px solid var(--slate-200); border-radius: var(--radius-lg); padding: 32px; box-shadow: var(--shadow-sm); text-align: left;">
            <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 20px;">
                <img src="<?php echo $baseUrl; ?>/assets/logo.svg" alt="LegalEase" width="48" height="48" style="border-radius: 8px;">
                <div>
                    <h3 style="font-size: 20px; margin: 0; color: var(--navy-900);">LegalEase</h3>
                    <p style="font-size: 14px; color: var(--slate-500); margin: 0;">AI-Powered Legal Document Generator</p>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; font-size: 14px; border-top: 1px solid var(--slate-200); padding-top: 20px;">
                <div>
                    <span style="display: block; font-size: 12px; color: var(--slate-400); text-transform: uppercase; font-weight: 700;">Developer</span>
                    <strong style="color: var(--navy-900);">Vaishnavi G</strong>
                </div>
                <div>
                    <span style="display: block; font-size: 12px; color: var(--slate-400); text-transform: uppercase; font-weight: 700;">Course</span>
                    <strong style="color: var(--navy-900);">B.Sc. Computer Science (2nd Year)</strong>
                </div>
                <div style="grid-column: span 2;">
                    <span style="display: block; font-size: 12px; color: var(--slate-400); text-transform: uppercase; font-weight: 700;">Institution</span>
                    <strong style="color: var(--navy-900);">Government Arts College (Autonomous), Kumbakonam</strong>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
