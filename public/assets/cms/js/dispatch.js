(function () {
    const form = document.getElementById('dp-scan-form');
    const input = document.getElementById('dp-barcode-input');
    const feedback = document.getElementById('dp-scan-feedback');
    const readyBanner = document.getElementById('dp-ready-banner');
    const cameraToggle = document.getElementById('dp-camera-toggle');
    const qrReaderEl = document.getElementById('dp-qr-reader');

    if (!form || !input) {
        return;
    }

    const csrf = window.cmsDispatchScan?.csrf || '';
    let html5QrCode = null;
    let cameraActive = false;

    function showFeedback(ok, message) {
        if (!feedback) {
            return;
        }

        feedback.hidden = false;
        feedback.className = 'dp-scan-feedback ' + (ok ? 'dp-scan-feedback--ok' : 'dp-scan-feedback--error');
        feedback.innerHTML = '<strong>' + (ok ? 'Scanned' : 'Error') + '</strong> ' + message;
    }

    function updateProgress(progress) {
        if (!progress) {
            return;
        }

        const count = document.getElementById('dp-progress-count');
        const fill = document.getElementById('dp-progress-fill');
        const percent = document.getElementById('dp-progress-percent');
        const remaining = document.getElementById('dp-progress-remaining');

        if (count) count.textContent = progress.scanned;
        if (fill) fill.style.width = progress.percent + '%';
        if (percent) percent.textContent = progress.percent + '% complete';
        if (remaining) remaining.textContent = progress.remaining + ' remaining';
    }

    function updateRow(productName, scanned, quantity) {
        document.querySelectorAll('#dp-items-body tr').forEach((row) => {
            const nameCell = row.querySelector('td');
            if (!nameCell || nameCell.textContent.trim() !== productName) {
                return;
            }

            const scannedCell = row.querySelector('.dp-scanned-count');
            if (scannedCell) {
                scannedCell.textContent = scanned;
            }

            if (scanned >= quantity) {
                row.classList.add('dp-item-row--done');
                const actionCell = row.querySelector('td:last-child');
                if (actionCell) {
                    actionCell.innerHTML = '<span class="dp-verified">Verified</span>';
                }
            }
        });
    }

    async function postScan(url, body) {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf,
            },
            body: JSON.stringify(body),
        });

        return response.json();
    }

    async function submitCode(code) {
        const trimmed = (code || '').trim();

        if (!trimmed) {
            showFeedback(false, 'Enter or scan a QR code.');
            return;
        }

        try {
            const data = await postScan(form.dataset.url, { code: trimmed });
            showFeedback(data.ok, data.message);

            if (data.ok) {
                updateProgress(data.progress);
                updateRow(data.product_name, data.scanned, data.quantity);
                input.value = '';
                input.focus();

                if (data.ready && readyBanner) {
                    readyBanner.hidden = false;
                }
            }
        } catch (error) {
            showFeedback(false, 'Scan failed. Please try again.');
        }
    }

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        await submitCode(input.value);
    });

    input.addEventListener('keydown', async (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            await submitCode(input.value);
        }
    });

    document.querySelectorAll('.dp-row-scan').forEach((button) => {
        button.addEventListener('click', async () => {
            try {
                const response = await fetch(button.dataset.url, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                    },
                });
                const data = await response.json();
                showFeedback(data.ok, data.message);

                if (data.ok) {
                    updateProgress(data.progress);
                    updateRow(data.product_name, data.scanned, data.quantity);

                    if (data.ready && readyBanner) {
                        readyBanner.hidden = false;
                    }
                }
            } catch (error) {
                showFeedback(false, 'Scan failed. Please try again.');
            }
        });
    });

    async function stopCamera() {
        if (!html5QrCode || !cameraActive) {
            return;
        }

        try {
            await html5QrCode.stop();
        } catch (error) {
            // ignore stop errors
        }

        cameraActive = false;
        if (qrReaderEl) {
            qrReaderEl.hidden = true;
        }
        if (cameraToggle) {
            cameraToggle.textContent = 'Use camera';
        }
    }

    async function startCamera() {
        if (typeof Html5Qrcode === 'undefined' || !qrReaderEl) {
            showFeedback(false, 'Camera scanner failed to load.');
            return;
        }

        qrReaderEl.hidden = false;

        if (!html5QrCode) {
            html5QrCode = new Html5Qrcode('dp-qr-reader');
        }

        try {
            await html5QrCode.start(
                { facingMode: 'environment' },
                { fps: 10, qrbox: { width: 220, height: 220 } },
                (decodedText) => {
                    input.value = decodedText;
                    submitCode(decodedText);
                },
                () => {}
            );
            cameraActive = true;
            if (cameraToggle) {
                cameraToggle.textContent = 'Stop camera';
            }
        } catch (error) {
            showFeedback(false, 'Could not access camera. Use a scanner or type the code.');
            qrReaderEl.hidden = true;
        }
    }

    if (cameraToggle) {
        cameraToggle.addEventListener('click', async () => {
            if (cameraActive) {
                await stopCamera();
            } else {
                await startCamera();
            }
        });
    }

    window.addEventListener('beforeunload', () => {
        stopCamera();
    });
})();
