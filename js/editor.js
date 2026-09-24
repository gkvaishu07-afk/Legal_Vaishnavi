/**
 * LegalEase - In-Place Document Editor
 * Allows direct in-browser editing of the generated legal document,
 * clause tweaking, typo fixing, and persistent saving without page reloads.
 */

document.addEventListener('DOMContentLoaded', () => {
    const btnEdit = document.getElementById('btnEditDoc');
    const btnSave = document.getElementById('btnSaveEdit');
    const btnCancel = document.getElementById('btnCancelEdit');
    const contentArea = document.getElementById('documentContentArea');
    const canvasWrap = document.getElementById('canvasWrap');
    const editBanner = document.getElementById('editModeBanner');
    const docStatusBadge = document.getElementById('docStatusBadge');

    // Export buttons to disable during active editing
    const exportButtons = [
        document.getElementById('btnPrintPdf'),
        document.getElementById('btnDownloadDocx'),
        document.getElementById('btnDownloadHtml'),
        document.getElementById('btnDownloadTxt'),
        document.getElementById('btnCopyText')
    ];

    let cachedOriginalHtml = '';

    /**
     * Enable In-Place Editing Mode
     */
    function enableEditMode() {
        if (!contentArea || !window.currentDocument) return;

        // Cache current content for cancel restoration
        cachedOriginalHtml = contentArea.innerHTML;

        // Make content editable
        contentArea.contentEditable = 'true';
        contentArea.focus();

        // Update UI
        if (canvasWrap) canvasWrap.classList.add('is-editing');
        if (editBanner) editBanner.style.display = 'flex';

        if (btnEdit) btnEdit.style.display = 'none';
        if (btnSave) btnSave.style.display = 'inline-flex';
        if (btnCancel) btnCancel.style.display = 'inline-flex';

        // Disable export actions while editing
        exportButtons.forEach(btn => {
            if (btn) btn.disabled = true;
        });

        if (docStatusBadge) {
            docStatusBadge.textContent = 'Editing Mode';
            docStatusBadge.style.backgroundColor = 'var(--gold-light)';
            docStatusBadge.style.color = 'var(--gold-hover)';
        }

        if (window.showToast) {
            window.showToast('Document is now editable. Make any changes directly on the canvas.', 'info');
        }
    }

    /**
     * Disable Editing Mode and save or cancel
     * @param {boolean} shouldSave True to commit changes, false to revert
     */
    window.disableEditMode = function(shouldSave) {
        if (!contentArea) return;

        if (shouldSave) {
            // Commit changes
            const newHtml = contentArea.innerHTML;
            if (window.currentDocument) {
                window.currentDocument.html = newHtml;
                // Update text representation
                window.currentDocument.text = contentArea.innerText;

                // Update localStorage history if this doc exists in history
                if (window.currentHistoryId && typeof window.updateDocumentInHistory === 'function') {
                    window.updateDocumentInHistory(window.currentHistoryId, newHtml, window.currentDocument.text);
                }
            }

            if (window.showToast) {
                window.showToast('Document modifications saved successfully!', 'success');
            }
        } else {
            // Revert changes if cancelled and cache exists
            if (cachedOriginalHtml) {
                contentArea.innerHTML = cachedOriginalHtml;
            }
            if (window.showToast) {
                window.showToast('Edit discarded. Reverted to previous version.', 'info');
            }
        }

        // Lock content again
        contentArea.contentEditable = 'false';

        // Revert UI classes & buttons
        if (canvasWrap) canvasWrap.classList.remove('is-editing');
        if (editBanner) editBanner.style.display = 'none';

        if (btnEdit) btnEdit.style.display = 'inline-flex';
        if (btnSave) btnSave.style.display = 'none';
        if (btnCancel) btnCancel.style.display = 'none';

        // Re-enable export actions
        exportButtons.forEach(btn => {
            if (btn) btn.disabled = false;
        });

        if (docStatusBadge) {
            docStatusBadge.textContent = 'Generated & Ready';
            docStatusBadge.style.backgroundColor = 'var(--emerald-light)';
            docStatusBadge.style.color = 'var(--emerald)';
        }
    };

    // Event Listeners
    if (btnEdit) {
        btnEdit.addEventListener('click', enableEditMode);
    }

    if (btnSave) {
        btnSave.addEventListener('click', () => window.disableEditMode(true));
    }

    if (btnCancel) {
        btnCancel.addEventListener('click', () => window.disableEditMode(false));
    }
});
