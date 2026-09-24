/**
 * LegalEase - Document Generation Engine & Form Handling
 * Vanilla JavaScript - Manages validation, AJAX fetch() to generate.php,
 * loading states, sample data population, and export triggers.
 */

// Global state of currently active document
window.currentDocument = null;

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('documentForm');
    const docTypeSelect = document.getElementById('docTypeSelect');
    const partiesInput = document.getElementById('partiesInput');
    const termsInput = document.getElementById('termsInput');
    const effectiveDateInput = document.getElementById('effectiveDateInput');
    const notesInput = document.getElementById('additionalNotesInput');
    const btnGenerate = document.getElementById('btnGenerate');
    const btnFillSample = document.getElementById('btnFillSample');

    // UI Elements
    const emptyState = document.getElementById('emptyState');
    const loadingOverlay = document.getElementById('loadingOverlay');
    const contentArea = document.getElementById('documentContentArea');
    const docStatusBadge = document.getElementById('docStatusBadge');
    const docModeBadge = document.getElementById('docModeBadge');

    // Toolbar Buttons
    const btnEditDoc = document.getElementById('btnEditDoc');
    const btnPrintPdf = document.getElementById('btnPrintPdf');
    const btnDownloadDocx = document.getElementById('btnDownloadDocx');
    const btnDownloadHtml = document.getElementById('btnDownloadHtml');
    const btnDownloadTxt = document.getElementById('btnDownloadTxt');
    const btnCopyText = document.getElementById('btnCopyText');

    // Sample data map for 1-click test fill
    const sampleDataMap = {
        'Freelance Work Contract': {
            parties: 'Jane Doe, Lead Full-Stack Engineer (Contractor); TechNova Solutions Inc., represented by Marcus Vance, CEO (Client)',
            terms: 'Contractor shall develop and deliver the custom cloud web platform within 60 calendar days;\nClient agrees to remit total compensation of $5,000 payable in two equal milestones: 50% upfront deposit and 50% upon final acceptance;\nAll intellectual property and code created shall transfer unconditionally to Client upon receipt of full payment;\nEither party may terminate upon 14 days prior written notice;\nBoth parties maintain strict non-disclosure of proprietary software architecture and client records.',
            notes: 'Include standard provision for 30 days of complimentary post-launch bug fixing.'
        },
        'Service Agreement': {
            parties: 'Apex Cloud Systems LLC, Service Provider; Horizon Global Logistics Ltd., Client',
            terms: 'Provider shall maintain 99.9% uptime SLA for managed cloud infrastructure;\nMonthly retainer fee of $1,500 invoiced on the 1st of each calendar month with Net 15 payment terms;\nCustomer support will provide 24/7 critical incident response within 60 minutes;\nService credits will apply toward subsequent invoice in event of SLA downtime;\nAgreement automatically renews annually unless terminated with 30 days written notice.',
            notes: 'Customer data must remain hosted strictly within domestic sovereign data centers.'
        },
        'Non-Disclosure Agreement (NDA)': {
            parties: 'Aegis Biotech Laboratories Inc. (Disclosing Party); Quantum Capital Ventures LLC (Receiving Party)',
            terms: 'Confidential Information includes all molecular research, patented trial data, financial pro-formas, and proprietary algorithms;\nReceiving Party agrees to exercise highest degree of care and restrict disclosure strictly to partners with a verified need-to-know;\nNon-disclosure and non-use covenants shall survive for 3 years from the date of disclosure;\nIn event of legal subpoena, Receiving Party must notify Disclosing Party within 48 hours;\nAll physical and digital records shall be returned or securely certified destroyed within 10 days of request.',
            notes: 'Mutual non-disclosure with injunctive relief rights in event of actual or threatened breach.'
        },
        'Business Agreement': {
            parties: 'Sterling Freight Corporation (Party A); Pacific Wholesale Network Ltd. (Party B)',
            terms: 'Party A shall serve as the exclusive regional distributor across Western trade zones;\nParty B commits to minimum quarterly inventory purchases of $75,000;\nNet 30 day settlement terms apply to all verified invoices;\nBi-annual performance review meetings shall be held by executive managers;\nNeither party shall solicit or hire employees of the other party during this agreement and for 12 months thereafter.',
            notes: 'Disputes to be resolved by binding commercial arbitration.'
        },
        'Lease Agreement': {
            parties: 'Oakridge Real Estate Holdings LLC (Landlord); Sarah Jenkins & Marcus Vance (Tenant)',
            terms: 'Leased premises located at Suite 402, 100 Main Boulevard Commercial Complex;\nMonthly rent shall be $2,400 due promptly on the 1st day of each month;\nSecurity deposit of $3,500 due upon signing and refundable within 21 days of lease conclusion;\nTenant shall not sublease premises without prior written consent from Landlord;\nQuiet hours observed between 10:00 PM and 7:00 AM;\nLandlord responsible for structural upkeep and common area utilities.',
            notes: 'One reserved covered parking space included.'
        },
        'Employment Offer Letter': {
            parties: 'Crestline Innovations Corp., represented by Elena Vance, Head of People (Employer); Alex Morgan (Candidate)',
            terms: 'Position Title: Senior Systems Engineer reporting directly to the Chief Technology Officer;\nStarting base salary of $110,000 per annum paid on standard bi-weekly payroll;\nEligibility for company health, dental, vision coverage, and 4% 401(k) retirement matching;\n18 days paid time off (PTO) accrued per calendar year;\nStandard 90-day introductory probationary period with performance review;\nEmployment is at-will and subject to standard background verification.',
            notes: 'Anticipated start date within 3 weeks of execution.'
        },
        'Partnership Agreement': {
            parties: 'David Chen (First Partner); Priya Sharma (Second Partner), establishing Beacon Design Studio',
            terms: 'Capital contributions: First Partner contributes $20,000 (50% interest) and Second Partner contributes $20,000 (50% interest);\nNet profits, distributions, and losses shall be apportioned equally 50/50;\nExpenditures exceeding $3,000 require unanimous written sign-off from both partners;\nFirst Partner manages technical operations and Second Partner directs marketing and client relations;\nIn event of withdrawal, remaining partner holds right of first refusal to acquire exiting equity.',
            notes: 'Both partners agree to devote full-time professional efforts.'
        },
        'General Contract': {
            parties: 'Universal Logistics Group (First Party); Nexus Supply Chain Inc. (Second Party)',
            terms: 'Both parties agree to execute their agreed deliverables in accordance with industry benchmarks;\nInvoicing shall occur on the 15th of each calendar month with payment within 30 days;\nNeither party may assign contractual rights without written consent;\nAny dispute shall first undergo 30 days of good-faith conciliation;\nGoverning law shall be the laws of the applicable mutual jurisdiction.',
            notes: 'Contract may be amended only by written instrument signed by both parties.'
        }
    };

    // Real-Time Helper Elements
    const docTypeCategoryBadge = document.getElementById('docTypeCategoryBadge');
    const partiesCountBadge = document.getElementById('partiesCountBadge');
    const termsCountBadge = document.getElementById('termsCountBadge');
    const dateStatusBadge = document.getElementById('dateStatusBadge');

    const VALID_CHECK_SVG = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>';
    const INVALID_ALERT_SVG = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#f43f5e" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>';

    // Pre-fill form if sample button clicked
    if (btnFillSample) {
        btnFillSample.addEventListener('click', () => {
            const selectedType = docTypeSelect.value || 'Freelance Work Contract';
            // If nothing selected, select Freelance Work Contract
            if (!docTypeSelect.value) {
                docTypeSelect.value = 'Freelance Work Contract';
            }
            
            const sample = sampleDataMap[docTypeSelect.value] || sampleDataMap['Freelance Work Contract'];
            partiesInput.value = sample.parties;
            termsInput.value = sample.terms;
            if (!effectiveDateInput.value) {
                effectiveDateInput.value = new Date().toISOString().split('T')[0];
            }
            notesInput.value = sample.notes;

            // Trigger ripple effect on button
            btnFillSample.classList.add('btn-sample-pop');
            setTimeout(() => btnFillSample.classList.remove('btn-sample-pop'), 400);

            // Real-time validate all filled fields to green active state
            validateField('document_type');
            validateField('parties');
            validateField('terms');
            validateField('effective_date');

            window.showToast(`Populated verified legal terms for "${docTypeSelect.value}".`, 'info');
        });
    }

    // Attach real-time validation listeners
    if (docTypeSelect) {
        docTypeSelect.addEventListener('change', () => validateField('document_type'));
    }

    if (partiesInput) {
        partiesInput.addEventListener('input', () => validateField('parties'));
        partiesInput.addEventListener('blur', () => validateField('parties'));
    }

    if (termsInput) {
        termsInput.addEventListener('input', () => validateField('terms'));
        termsInput.addEventListener('blur', () => validateField('terms'));
    }

    if (effectiveDateInput) {
        effectiveDateInput.addEventListener('change', () => validateField('effective_date'));
        effectiveDateInput.addEventListener('input', () => validateField('effective_date'));
    }

    // Check DOCX availability on load
    const baseUrl = window.LEGALEASE_BASE_URL || '';
    fetch(`${baseUrl}/download.php?check_docx=1`)
        .then(res => res.json())
        .then(data => {
            if (!data.zip_available && btnDownloadDocx) {
                btnDownloadDocx.title = 'DOCX export requires PHP ZipArchive extension.';
            }
        })
        .catch(() => {});

    // Initial validation check if pre-selected type passed
    if (docTypeSelect && docTypeSelect.value) {
        validateField('document_type');
    }
    if (effectiveDateInput && effectiveDateInput.value) {
        validateField('effective_date');
    }

    // FORM SUBMISSION & VALIDATION
    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            // Client-side Validation across all fields
            const isValid = validateAllFields();
            if (!isValid) {
                // Find first invalid input, scroll to it, focus it, and shake it
                const firstInvalid = form.querySelector('.form-control.is-invalid');
                if (firstInvalid) {
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstInvalid.focus();
                    firstInvalid.classList.add('shake-error');
                    setTimeout(() => firstInvalid.classList.remove('shake-error'), 500);
                }
                window.showToast('Please correct the highlighted fields before generating.', 'error');
                return;
            }

            // Prepare Payload
            const payload = {
                document_type: docTypeSelect.value.trim(),
                parties: partiesInput.value.trim(),
                terms: termsInput.value.trim(),
                effective_date: effectiveDateInput.value.trim(),
                additional_notes: notesInput.value.trim()
            };

            // Set UI Loading State
            setLoadingState(true);

            try {
                const response = await fetch(`${baseUrl}/generate.php`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const result = await response.json();

                if (!response.ok || !result.success) {
                    // Check if server-side validation errors were returned
                    if (result.errors) {
                        displayServerErrors(result.errors);
                        window.showToast(result.message || 'Validation failed. Check your entries.', 'error');
                    } else {
                        window.showToast(result.message || 'An error occurred during generation.', 'error');
                    }
                    setLoadingState(false);
                    return;
                }

                // Document Generated Successfully!
                renderGeneratedDocument(result);

                // Save to localStorage history
                const saved = saveDocumentToHistory({
                    title: result.document.title,
                    document_type: result.document.document_type,
                    effective_date: result.document.effective_date,
                    parties: result.document.parties,
                    html: result.document.html,
                    text: result.document.text,
                    mode: result.mode
                });

                if (saved) {
                    window.currentHistoryId = saved.id;
                }

                // Notify User with colorful toast
                if (result.fallback_triggered) {
                    window.showToast(result.fallback_reason || 'AI temporarily unavailable. Generated using built-in template system.', 'warning', 6000);
                } else if (result.mode === 'ai') {
                    window.showToast('Document drafted successfully with Google Gemini AI!', 'success');
                } else {
                    window.showToast('Document generated successfully using verified legal templates.', 'success');
                }

            } catch (err) {
                console.error('Generation fetch error:', err);
                window.showToast('Network error or server connection failed. Please try again.', 'error');
            } finally {
                setLoadingState(false);
            }
        });
    }

    /**
     * Validate an individual field with colorful real-time feedback
     * @param {'document_type'|'parties'|'terms'|'effective_date'} fieldName
     * @returns {boolean}
     */
    function validateField(fieldName) {
        let isValid = true;
        let errorMessage = '';

        if (fieldName === 'document_type') {
            const val = docTypeSelect ? docTypeSelect.value.trim() : '';
            if (!val) {
                isValid = false;
                errorMessage = 'Please select a document type.';
                if (docTypeCategoryBadge) docTypeCategoryBadge.style.display = 'none';
            } else {
                isValid = true;
                const opt = docTypeSelect.options[docTypeSelect.selectedIndex];
                const cat = opt ? opt.getAttribute('data-category') : 'Legal Document';
                if (docTypeCategoryBadge) {
                    docTypeCategoryBadge.textContent = '📂 ' + (cat || 'Legal Instrument');
                    docTypeCategoryBadge.className = 'field-meta-pill pill-category';
                    docTypeCategoryBadge.style.display = 'inline-flex';
                }
            }
        } else if (fieldName === 'parties') {
            const val = partiesInput ? partiesInput.value.trim() : '';
            if (!val) {
                isValid = false;
                errorMessage = 'Parties involved cannot be empty.';
                if (partiesCountBadge) partiesCountBadge.style.display = 'none';
            } else if (val.length < 4) {
                isValid = false;
                errorMessage = 'Please provide full party names (at least 4 characters).';
                if (partiesCountBadge) {
                    partiesCountBadge.textContent = `⚠️ ${val.length}/4 min chars`;
                    partiesCountBadge.className = 'field-meta-pill pill-warning';
                    partiesCountBadge.style.display = 'inline-flex';
                }
            } else {
                isValid = true;
                // Count parties by comma, semicolon, or 'and'
                const detectedParties = val.split(/[,;\n]+|\band\b/i).map(s => s.trim()).filter(Boolean);
                const count = detectedParties.length;
                if (partiesCountBadge) {
                    partiesCountBadge.textContent = count > 1 ? `✓ ${count} Parties Detected` : '✓ 1 Party Listed';
                    partiesCountBadge.className = 'field-meta-pill pill-success';
                    partiesCountBadge.style.display = 'inline-flex';
                }
            }
        } else if (fieldName === 'terms') {
            const val = termsInput ? termsInput.value.trim() : '';
            if (!val) {
                isValid = false;
                errorMessage = 'Please provide agreed terms and conditions.';
                if (termsCountBadge) termsCountBadge.style.display = 'none';
            } else if (val.length < 10) {
                isValid = false;
                errorMessage = 'Terms and conditions must be at least 10 characters long.';
                if (termsCountBadge) {
                    termsCountBadge.textContent = `⚠️ ${val.length}/10 min chars`;
                    termsCountBadge.className = 'field-meta-pill pill-warning';
                    termsCountBadge.style.display = 'inline-flex';
                }
            } else {
                isValid = true;
                // Count distinct clauses by semicolon or newline
                const clauses = val.split(/[;\n\r]+/).map(s => s.trim()).filter(Boolean);
                const clauseCount = clauses.length;
                if (termsCountBadge) {
                    termsCountBadge.textContent = `✓ ${clauseCount} Clause${clauseCount !== 1 ? 's' : ''} (${val.length} chars)`;
                    termsCountBadge.className = 'field-meta-pill pill-success';
                    termsCountBadge.style.display = 'inline-flex';
                }
            }
        } else if (fieldName === 'effective_date') {
            const val = effectiveDateInput ? effectiveDateInput.value.trim() : '';
            if (!val) {
                isValid = false;
                errorMessage = 'Effective date is required.';
                if (dateStatusBadge) dateStatusBadge.style.display = 'none';
            } else {
                const parsed = new Date(val);
                if (isNaN(parsed.getTime())) {
                    isValid = false;
                    errorMessage = 'Please enter a valid calendar date.';
                    if (dateStatusBadge) dateStatusBadge.style.display = 'none';
                } else {
                    isValid = true;
                    if (dateStatusBadge) {
                        const dateFormatted = parsed.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                        dateStatusBadge.textContent = `✓ Date: ${dateFormatted}`;
                        dateStatusBadge.className = 'field-meta-pill pill-success';
                        dateStatusBadge.style.display = 'inline-flex';
                    }
                }
            }
        }

        const input = document.querySelector(`[name="${fieldName}"]`);
        const errElem = document.getElementById(`err-${fieldName}`);
        const statusIcon = document.getElementById(`status-icon-${fieldName}`);

        if (input) {
            if (isValid) {
                input.classList.remove('is-invalid');
                input.classList.add('is-valid');
            } else {
                input.classList.remove('is-valid');
                input.classList.add('is-invalid');
            }
        }

        if (errElem) {
            const spanText = errElem.querySelector('span');
            if (spanText) {
                spanText.textContent = errorMessage;
            } else {
                errElem.textContent = errorMessage;
            }
            errElem.style.display = isValid ? 'none' : 'flex';
        }

        if (statusIcon) {
            statusIcon.innerHTML = isValid ? VALID_CHECK_SVG : (errorMessage ? INVALID_ALERT_SVG : '');
            statusIcon.className = `field-status-icon ${isValid ? 'status-valid' : 'status-invalid'}`;
            statusIcon.style.display = (isValid || errorMessage) ? 'inline-flex' : 'none';
        }

        return isValid;
    }

    /**
     * Validate all required fields
     * @returns {boolean}
     */
    function validateAllFields() {
        const f1 = validateField('document_type');
        const f2 = validateField('parties');
        const f3 = validateField('terms');
        const f4 = validateField('effective_date');
        return f1 && f2 && f3 && f4;
    }

    function clearFieldError(fieldName) {
        const input = document.querySelector(`[name="${fieldName}"]`);
        const errElem = document.getElementById(`err-${fieldName}`);
        const statusIcon = document.getElementById(`status-icon-${fieldName}`);
        if (input) {
            input.classList.remove('is-invalid', 'is-valid');
        }
        if (errElem) {
            errElem.style.display = 'none';
        }
        if (statusIcon) {
            statusIcon.style.display = 'none';
            statusIcon.innerHTML = '';
        }
    }

    function clearFormErrors() {
        ['document_type', 'parties', 'terms', 'effective_date'].forEach(clearFieldError);
    }

    function displayServerErrors(errors) {
        for (const [key, msg] of Object.entries(errors)) {
            const input = document.querySelector(`[name="${key}"]`);
            const errElem = document.getElementById(`err-${key}`);
            const statusIcon = document.getElementById(`status-icon-${key}`);
            if (input) {
                input.classList.remove('is-valid');
                input.classList.add('is-invalid');
            }
            if (errElem) {
                const spanText = errElem.querySelector('span');
                if (spanText) spanText.textContent = msg;
                else errElem.textContent = msg;
                errElem.style.display = 'flex';
            }
            if (statusIcon) {
                statusIcon.innerHTML = INVALID_ALERT_SVG;
                statusIcon.className = 'field-status-icon status-invalid';
                statusIcon.style.display = 'inline-flex';
            }
        }
    }

    /**
     * Toggle UI loading states
     * @param {boolean} isLoading
     */
    function setLoadingState(isLoading) {
        if (btnGenerate) {
            btnGenerate.disabled = isLoading;
            const textSpan = btnGenerate.querySelector('.btn-text');
            const spinnerSpan = btnGenerate.querySelector('.btn-spinner');
            if (textSpan) textSpan.style.display = isLoading ? 'none' : 'inline-flex';
            if (spinnerSpan) spinnerSpan.style.display = isLoading ? 'inline-flex' : 'none';
        }

        if (loadingOverlay) {
            loadingOverlay.style.display = isLoading ? 'flex' : 'none';
        }
    }

    /**
     * Render the generated document into the live paper container
     * @param {Object} result Response from generate.php
     */
    function renderGeneratedDocument(result) {
        window.currentDocument = result.document;

        // Hide empty state & show content
        if (emptyState) emptyState.style.display = 'none';
        if (contentArea) {
            contentArea.innerHTML = result.document.html;
            contentArea.style.display = 'block';
        }

        // Update toolbar status badges
        if (docStatusBadge) {
            docStatusBadge.textContent = 'Generated & Ready';
            docStatusBadge.style.backgroundColor = 'var(--emerald-light)';
            docStatusBadge.style.color = 'var(--emerald)';
        }

        if (docModeBadge) {
            docModeBadge.style.display = 'inline-flex';
            if (result.mode === 'ai') {
                docModeBadge.textContent = 'Google Gemini AI';
                docModeBadge.className = 'badge badge-blue';
            } else {
                docModeBadge.textContent = 'Built-in Template';
                docModeBadge.className = 'badge badge-gold';
            }
        }

        // Enable all toolbar buttons
        [btnEditDoc, btnPrintPdf, btnDownloadDocx, btnDownloadHtml, btnDownloadTxt, btnCopyText].forEach(btn => {
            if (btn) btn.disabled = false;
        });

        // Show Post-Generation Action Bar with quick Back to Home control
        const postGenBar = document.getElementById('postGenerationBar');
        if (postGenBar) {
            postGenBar.style.display = 'flex';
        }

        // Ensure edit mode is closed if previously active
        if (window.disableEditMode) {
            window.disableEditMode(false);
        }

        // Scroll smoothly to preview on mobile/tablets
        if (window.innerWidth <= 900) {
            contentArea.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    /**
     * Load a document from history directly into the workspace
     * @param {Object} docRecord Document record from localStorage
     */
    window.loadDocumentIntoWorkspace = function(docRecord) {
        if (!docRecord) return;

        // Populate form if matching values exist
        if (docTypeSelect && docRecord.document_type) {
            for (let i = 0; i < docTypeSelect.options.length; i++) {
                if (docTypeSelect.options[i].value === docRecord.document_type) {
                    docTypeSelect.selectedIndex = i;
                    break;
                }
            }
        }
        if (partiesInput && docRecord.parties) {
            partiesInput.value = docRecord.parties;
        }
        if (effectiveDateInput && docRecord.effective_date) {
            effectiveDateInput.value = docRecord.effective_date;
        }

        window.currentHistoryId = docRecord.id;

        renderGeneratedDocument({
            success: true,
            mode: docRecord.mode || 'template',
            document: {
                title: docRecord.title,
                document_type: docRecord.document_type,
                effective_date: docRecord.effective_date,
                parties: docRecord.parties,
                html: docRecord.html,
                text: docRecord.text
            }
        });
    };

    // EXPORT ACTION HANDLERS

    // 1. Native Print / Save as PDF
    if (btnPrintPdf) {
        btnPrintPdf.addEventListener('click', () => {
            if (!window.currentDocument) return;
            // Native browser print dialog executes print stylesheet
            window.print();
            setTimeout(() => {
                window.showToast('Document ready! Click "Back to Home" anytime to return to dashboard.', 'info', 5000);
            }, 600);
        });
    }

    // Quick Print from Post-Generation Bar
    const btnQuickPrint = document.getElementById('btnQuickPrint');
    if (btnQuickPrint) {
        btnQuickPrint.addEventListener('click', () => {
            if (btnPrintPdf) btnPrintPdf.click();
        });
    }

    // 2. Trigger Exports via Hidden Form
    function triggerDownload(format) {
        if (!window.currentDocument) {
            window.showToast('Please generate a document first.', 'warning');
            return;
        }

        const exportForm = document.getElementById('exportForm');
        const formatInput = document.getElementById('exportFormat');
        const titleInput = document.getElementById('exportTitle');
        const htmlInput = document.getElementById('exportHtml');
        const textInput = document.getElementById('exportText');

        if (!exportForm) return;

        formatInput.value = format;
        titleInput.value = window.currentDocument.title || 'Legal_Document';
        // Use live edited HTML if updated
        htmlInput.value = contentArea ? contentArea.innerHTML : (window.currentDocument.html || '');
        textInput.value = window.currentDocument.text || '';

        exportForm.submit();
        window.showToast(`Downloading ${format.toUpperCase()} document...`, 'info');
    }

    if (btnDownloadTxt) {
        btnDownloadTxt.addEventListener('click', () => triggerDownload('txt'));
    }

    if (btnDownloadHtml) {
        btnDownloadHtml.addEventListener('click', () => triggerDownload('html'));
    }

    if (btnDownloadDocx) {
        btnDownloadDocx.addEventListener('click', () => triggerDownload('docx'));
    }

    // 3. Copy Plain Text to Clipboard
    if (btnCopyText) {
        btnCopyText.addEventListener('click', async () => {
            if (!window.currentDocument) return;

            const textToCopy = window.currentDocument.text || (contentArea ? contentArea.innerText : '');
            try {
                await navigator.clipboard.writeText(textToCopy);
                window.showToast('Document text copied to clipboard!', 'success');
            } catch (err) {
                // Fallback for older browsers
                const textarea = document.createElement('textarea');
                textarea.value = textToCopy;
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand('copy');
                document.body.removeChild(textarea);
                window.showToast('Document text copied to clipboard!', 'success');
            }
        });
    }
});
