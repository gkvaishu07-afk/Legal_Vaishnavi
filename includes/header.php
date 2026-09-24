<?php
/**
 * LegalEase - Universal Header Component
 */

if (!defined('LEGALEASE_ACCESS')) {
    define('LEGALEASE_ACCESS', true);
}

require_once __DIR__ . '/helpers.php';

$baseUrl = getBaseUrl();
$isAiActive = isGeminiConfigured();
$currentPage = basename($_SERVER['PHP_SELF']);
$pageTitle = isset($pageTitle) ? $pageTitle . ' - LegalEase' : 'LegalEase – AI-Powered Legal Document Generator';
$pageDesc = isset($pageDesc) ? $pageDesc : 'Generate professional, structured legal contracts and agreements in seconds using LegalEase AI or verified legal templates.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($pageDesc); ?>">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="<?php echo htmlspecialchars($pageTitle); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($pageDesc); ?>">
    <meta property="og:type" content="website">
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="<?php echo $baseUrl; ?>/assets/logo.svg">
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>/css/style.css">
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>/css/responsive.css">
</head>
<body>
    <!-- Top Navigation Bar -->
    <header class="navbar" id="mainNavbar">
        <div class="container nav-container">
            <a href="<?php echo $baseUrl; ?>/index.php" class="nav-brand" aria-label="LegalEase Home">
                <img src="<?php echo $baseUrl; ?>/assets/logo.svg" alt="LegalEase Scales of Justice Logo" class="nav-logo" width="40" height="40">
                <div class="nav-brand-text">
                    <span class="brand-title">Legal<span>Ease</span></span>
                    <span class="brand-badge">AI Legal Tech</span>
                </div>
            </a>

            <!-- Desktop & Mobile Nav Links -->
            <nav class="nav-menu-wrapper">
                <ul class="nav-links" id="navLinks">
                    <li><a href="<?php echo $baseUrl; ?>/index.php" class="nav-link <?php echo ($currentPage === 'index.php') ? 'active' : ''; ?>">Home</a></li>
                    <li><a href="<?php echo $baseUrl; ?>/create.php" class="nav-link <?php echo ($currentPage === 'create.php') ? 'active' : ''; ?>">Create</a></li>
                    <li><a href="<?php echo ($currentPage === 'index.php') ? '#templates' : $baseUrl . '/index.php#templates'; ?>" class="nav-link">Templates</a></li>
                    <li><a href="<?php echo ($currentPage === 'index.php') ? '#how-it-works' : $baseUrl . '/index.php#how-it-works'; ?>" class="nav-link">How It Works</a></li>
                    <li><a href="<?php echo ($currentPage === 'index.php') ? '#about' : $baseUrl . '/index.php#about'; ?>" class="nav-link">About</a></li>
                    <li><a href="<?php echo ($currentPage === 'index.php') ? '#contact' : $baseUrl . '/index.php#contact'; ?>" class="nav-link">Contact</a></li>
                </ul>
            </nav>

            <!-- Status Indicator & CTA Actions -->
            <div class="nav-actions">
                <?php if ($isAiActive): ?>
                    <span class="mode-indicator ai-mode" title="Configured with Google Gemini API">
                        <span class="mode-pulse"></span>
                        AI Mode
                    </span>
                <?php else: ?>
                    <span class="mode-indicator demo-mode" title="Running with local templates.json fallback">
                        <span class="mode-pulse"></span>
                        Demo Mode
                    </span>
                <?php endif; ?>

                <!-- Document History Button -->
                <button type="button" class="btn btn-secondary btn-sm" id="btnToggleHistory" title="View previously created documents">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    History <span id="historyBadge" class="badge badge-blue" style="display:none; padding:2px 6px; font-size:10px;">0</span>
                </button>

                <a href="<?php echo $baseUrl; ?>/create.php" class="btn btn-primary btn-sm btn-nav-cta">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                    <span>New Document</span>
                </a>
            </div>

            <!-- Mobile Hamburger Toggle -->
            <button class="mobile-toggle" id="mobileMenuBtn" aria-label="Toggle navigation menu">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            </button>
        </div>
    </header>
