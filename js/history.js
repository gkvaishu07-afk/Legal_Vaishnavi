/**
 * LegalEase - Document History Management (localStorage)
 * Stores and manages previously generated legal documents client-side.
 */

const HISTORY_STORAGE_KEY = 'legalease_doc_history_v1';
const MAX_HISTORY_ITEMS = 25;

/**
 * Retrieve document history from localStorage
 * @returns {Array} List of document objects
 */
function getDocumentHistory() {
    try {
        const raw = localStorage.getItem(HISTORY_STORAGE_KEY);
        if (!raw) return [];
        const parsed = JSON.parse(raw);
        return Array.isArray(parsed) ? parsed : [];
    } catch (e) {
        console.warn('Could not read document history from localStorage:', e);
        return [];
    }
}

/**
 * Save a newly generated document to history
 * @param {Object} doc { title, document_type, effective_date, parties, html, text }
 * @returns {Object} Stored record with unique ID
 */
function saveDocumentToHistory(doc) {
    if (!doc || !doc.title) return null;

    try {
        const history = getDocumentHistory();
        const record = {
            id: 'doc_' + Date.now() + '_' + Math.random().toString(36).substring(2, 7),
            title: doc.title,
            document_type: doc.document_type || doc.title,
            effective_date: doc.effective_date || new Date().toISOString().split('T')[0],
            parties: doc.parties || '',
            html: doc.html || '',
            text: doc.text || '',
            mode: doc.mode || 'template',
            created_at: new Date().toISOString()
        };

        // Add to front of history
        history.unshift(record);

        // Keep within maximum cap
        if (history.length > MAX_HISTORY_ITEMS) {
            history.length = MAX_HISTORY_ITEMS;
        }

        localStorage.setItem(HISTORY_STORAGE_KEY, JSON.stringify(history));
        updateHistoryUI();
        return record;
    } catch (e) {
        console.warn('Could not save to localStorage:', e);
        return null;
    }
}

/**
 * Update an existing history item after manual in-place edits
 * @param {string} id
 * @param {string} newHtml
 * @param {string} newText
 */
function updateDocumentInHistory(id, newHtml, newText) {
    if (!id) return;
    try {
        const history = getDocumentHistory();
        const idx = history.findIndex(item => item.id === id);
        if (idx !== -1) {
            history[idx].html = newHtml;
            if (newText) history[idx].text = newText;
            history[idx].updated_at = new Date().toISOString();
            localStorage.setItem(HISTORY_STORAGE_KEY, JSON.stringify(history));
            updateHistoryUI();
        }
    } catch (e) {
        console.warn('Could not update history in localStorage:', e);
    }
}

/**
 * Delete a specific document from history
 * @param {string} id
 */
function deleteHistoryDocument(id) {
    try {
        let history = getDocumentHistory();
        history = history.filter(item => item.id !== id);
        localStorage.setItem(HISTORY_STORAGE_KEY, JSON.stringify(history));
        updateHistoryUI();
        if (window.showToast) {
            window.showToast('Document removed from history.', 'info');
        }
    } catch (e) {
        console.warn('Could not delete history item:', e);
    }
}

/**
 * Clear all document history
 */
function clearAllHistory() {
    if (confirm('Are you sure you want to clear all saved document history? This cannot be undone.')) {
        localStorage.removeItem(HISTORY_STORAGE_KEY);
        updateHistoryUI();
        if (window.showToast) {
            window.showToast('All document history has been cleared.', 'info');
        }
    }
}

/**
 * Update history drawer UI and badge counter
 */
function updateHistoryUI() {
    const history = getDocumentHistory();
    const badge = document.getElementById('historyBadge');
    const container = document.getElementById('historyListContainer');

    // Update count badge
    if (badge) {
        if (history.length > 0) {
            badge.textContent = history.length;
            badge.style.display = 'inline-block';
        } else {
            badge.style.display = 'none';
        }
    }

    if (!container) return;

    if (history.length === 0) {
        container.innerHTML = `
            <div style="text-align:center; padding: 40px 10px; color: var(--slate-400);">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom:12px;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                <p style="font-size:14px; font-weight:600; color:var(--navy-800); margin-bottom:4px;">No History Yet</p>
                <p style="font-size:12px;">Documents generated will appear here for easy viewing and re-downloading.</p>
            </div>
        `;
        return;
    }

    let html = '';
    history.forEach(item => {
        const dateFormatted = new Date(item.created_at).toLocaleDateString(undefined, {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
        const snippet = item.parties ? `Parties: ${item.parties.substring(0, 65)}...` : 'Legal Contract';

        html += `
            <div class="history-item" data-id="${item.id}">
                <div class="history-item-top">
                    <span class="history-item-title">${escapeHtml(item.title)}</span>
                    <span class="history-item-date">${dateFormatted}</span>
                </div>
                <p class="history-item-snippet">${escapeHtml(snippet)}</p>
                <div class="history-item-actions">
                    <button type="button" class="btn btn-secondary btn-sm" onclick="openHistoryItem('${item.id}')">
                        Open &bull; View
                    </button>
                    <button type="button" class="btn btn-danger btn-sm" onclick="deleteHistoryDocument('${item.id}')" title="Delete this entry">
                        Delete
                    </button>
                </div>
            </div>
        `;
    });

    container.innerHTML = html;
}

/**
 * Open a document from history
 * @param {string} id
 */
function openHistoryItem(id) {
    const history = getDocumentHistory();
    const item = history.find(d => d.id === id);
    if (!item) return;

    // Check if on create.php page
    if (typeof window.loadDocumentIntoWorkspace === 'function') {
        window.loadDocumentIntoWorkspace(item);
        closeHistoryDrawer();
        if (window.showToast) {
            window.showToast(`Loaded "${item.title}" into workspace.`, 'success');
        }
    } else {
        // We are on index.php or another page: store pending doc ID and redirect to create.php
        sessionStorage.setItem('legalease_pending_load', JSON.stringify(item));
        const base = window.LEGALEASE_BASE_URL || '';
        window.location.href = `${base}/create.php?load=${id}`;
    }
}

/**
 * Open history drawer
 */
function openHistoryDrawer() {
    updateHistoryUI();
    const drawer = document.getElementById('historyDrawerBackdrop');
    if (drawer) drawer.classList.add('active');
}

/**
 * Close history drawer
 */
function closeHistoryDrawer() {
    const drawer = document.getElementById('historyDrawerBackdrop');
    if (drawer) drawer.classList.remove('active');
}

// Simple HTML escaping helper for history display
function escapeHtml(str) {
    if (!str) return '';
    return str
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

// Global initialization
document.addEventListener('DOMContentLoaded', () => {
    updateHistoryUI();

    const btnToggle = document.getElementById('btnToggleHistory');
    if (btnToggle) btnToggle.addEventListener('click', openHistoryDrawer);

    const btnClose = document.getElementById('btnCloseHistory');
    if (btnClose) btnClose.addEventListener('click', closeHistoryDrawer);

    const btnCloseBtn = document.getElementById('btnCloseHistoryBtn');
    if (btnCloseBtn) btnCloseBtn.addEventListener('click', closeHistoryDrawer);

    const backdrop = document.getElementById('historyDrawerBackdrop');
    if (backdrop) {
        backdrop.addEventListener('click', (e) => {
            if (e.target === backdrop) closeHistoryDrawer();
        });
    }

    const btnClear = document.getElementById('btnClearHistory');
    if (btnClear) btnClear.addEventListener('click', clearAllHistory);
});
