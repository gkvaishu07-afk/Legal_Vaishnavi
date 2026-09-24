<?php
/**
 * LegalEase - Universal Footer Component
 */

if (!defined('LEGALEASE_ACCESS')) {
    define('LEGALEASE_ACCESS', true);
}

$baseUrl = getBaseUrl();
?>
    <!-- History Drawer Backdrop & Drawer -->
    <div class="history-drawer-backdrop" id="historyDrawerBackdrop">
        <aside class="history-drawer" aria-labelledby="historyDrawerTitle">
            <div class="drawer-header">
                <h3 class="drawer-title" id="historyDrawerTitle">Document History</h3>
                <button type="button" class="drawer-close" id="btnCloseHistory" aria-label="Close history drawer">&times;</button>
            </div>
            <div class="drawer-body">
                <div id="historyListContainer" class="history-list">
                    <!-- Populated by js/history.js -->
                </div>
            </div>
            <div class="drawer-footer">
                <button type="button" class="btn btn-secondary btn-sm" id="btnClearHistory">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                    Clear History
                </button>
                <button type="button" class="btn btn-primary btn-sm" id="btnCloseHistoryBtn">Close</button>
            </div>
        </aside>
    </div>

    <!-- Toast Notifications Container -->
    <div class="toast-container" id="toastContainer" aria-live="polite"></div>

    <!-- Main Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-top">
                <!-- Brand Column -->
                <div class="footer-brand">
                    <div class="footer-brand-title">Legal<span>Ease</span></div>
                    <p class="footer-desc">
                        Next-generation AI legal document generation platform designed to streamline and automate formal agreement drafting.
                    </p>
                    <div class="footer-badge-wrap">
                        <span class="badge badge-blue">B.Sc. CS 2nd Year Project</span>
                    </div>
                </div>

                <!-- Navigation Links -->
                <div>
                    <h4 class="footer-col-title">Navigation</h4>
                    <ul class="footer-links">
                        <li><a href="<?php echo $baseUrl; ?>/index.php">Home</a></li>
                        <li><a href="<?php echo $baseUrl; ?>/create.php">Create Document</a></li>
                        <li><a href="<?php echo $baseUrl; ?>/index.php#templates">Legal Templates</a></li>
                        <li><a href="<?php echo $baseUrl; ?>/index.php#how-it-works">How It Works</a></li>
                        <li><a href="<?php echo $baseUrl; ?>/index.php#about">About Project</a></li>
                    </ul>
                </div>

                <!-- Legal Contracts -->
                <div>
                    <h4 class="footer-col-title">Supported Types</h4>
                    <ul class="footer-links">
                        <li><a href="<?php echo $baseUrl; ?>/create.php?type=freelance_contract">Freelance Contract</a></li>
                        <li><a href="<?php echo $baseUrl; ?>/create.php?type=nda">Non-Disclosure (NDA)</a></li>
                        <li><a href="<?php echo $baseUrl; ?>/create.php?type=service_agreement">Service Agreement</a></li>
                        <li><a href="<?php echo $baseUrl; ?>/create.php?type=lease_agreement">Lease Agreement</a></li>
                        <li><a href="<?php echo $baseUrl; ?>/create.php?type=employment_offer">Employment Offer</a></li>
                    </ul>
                </div>

                <!-- Project Developer Card -->
                <div>
                    <h4 class="footer-col-title">Project Information</h4>
                    <div class="student-credit-box">
                        <div class="credit-title">Developed By</div>
                        <div class="credit-name">Vaishnavi G</div>
                        <div class="credit-inst">
                            Government Arts College (Autonomous), Kumbakonam<br>
                            B.Sc. Computer Science &bull; 2nd Year
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mandatory Legal Disclaimer -->
            <div class="footer-disclaimer-bar">
                <strong>IMPORTANT LEGAL DISCLAIMER:</strong> LegalEase is an AI-assisted document drafting tool. Generated documents are provided for informational and preliminary drafting purposes only and do not constitute formal legal advice. Always review generated contracts with a certified legal professional before execution.
            </div>

            <!-- Bottom Copyright Bar -->
            <div class="footer-bottom">
                <div>
                    &copy; <?php echo date('Y'); ?> LegalEase &bull; AI-Powered Legal Document Generator. All rights reserved.
                </div>
                <div>
                    Designed &amp; Developed with PHP, JavaScript &amp; CSS by <strong>Vaishnavi G</strong>
                </div>
            </div>
        </div>
    </footer>

    <!-- Core Scripts -->
    <script>
        // Expose base URL to frontend JavaScript
        window.LEGALEASE_BASE_URL = '<?php echo $baseUrl; ?>';
    </script>
    <script src="<?php echo $baseUrl; ?>/js/history.js"></script>
    <script src="<?php echo $baseUrl; ?>/js/app.js"></script>
    <?php if (isset($extraScripts) && is_array($extraScripts)): ?>
        <?php foreach ($extraScripts as $script): ?>
            <script src="<?php echo $baseUrl . '/' . $script; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
