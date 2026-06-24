(function () {
    const courier = document.getElementById('courier_key');
    const tracking = document.getElementById('tracking_number');
    const confirmBtn = document.getElementById('dp-confirm-dispatch');
    const summary = document.getElementById('dp-tracking-summary');
    const autoBtn = document.getElementById('dp-auto-tracking');
    const labelWrap = document.getElementById('dp-tracking-label');
    const labelPreview = document.getElementById('dp-tracking-label-preview');
    const qrImg = document.getElementById('dp-tracking-qr');
    const barcodeImg = document.getElementById('dp-tracking-barcode');
    const codeDisplay = document.getElementById('dp-tracking-code-display');
    const printBtn = document.getElementById('dp-print-label');
    const config = window.cmsDispatchShip || {};

    if (!courier || !tracking || !confirmBtn) {
        return;
    }

    function courierLabel() {
        const option = courier.options[courier.selectedIndex];
        return option && option.value ? option.textContent : '';
    }

    function trackingValue() {
        return tracking.value.trim();
    }

    function updateLabelPreview() {
        const value = trackingValue();

        if (!value || !labelWrap || !qrImg || !codeDisplay) {
            if (labelWrap) {
                labelWrap.hidden = true;
            }
            return;
        }

        labelWrap.hidden = false;
        qrImg.src = (config.qrBase || '') + encodeURIComponent(value);
        qrImg.alt = 'Tracking QR ' + value;
        codeDisplay.textContent = value;

        if (barcodeImg && config.barcodeUrl) {
            barcodeImg.hidden = false;
            barcodeImg.src = config.barcodeUrl + '?code=' + encodeURIComponent(value);
        }
    }

    function syncState() {
        const hasCourier = Boolean(courier.value);
        tracking.disabled = !hasCourier;

        if (!hasCourier) {
            tracking.value = '';
        }

        const ready = hasCourier && trackingValue() !== '';
        confirmBtn.disabled = !ready;

        if (ready && summary) {
            summary.hidden = false;
            summary.textContent = courierLabel() + ' · ' + trackingValue();
        } else if (summary) {
            summary.hidden = true;
            summary.textContent = '';
        }

        updateLabelPreview();
    }

    courier.addEventListener('change', syncState);
    tracking.addEventListener('input', syncState);

    if (autoBtn) {
        autoBtn.addEventListener('click', () => {
            if (!courier.value) {
                courier.focus();
                return;
            }

            tracking.value = 'MBS-TRK-' + String(Math.floor(1000000 + Math.random() * 9000000));
            tracking.disabled = false;
            syncState();
        });
    }

    if (printBtn && labelPreview) {
        printBtn.addEventListener('click', () => {
            const value = trackingValue();

            if (!value) {
                return;
            }

            const qrSrc = (config.qrBase || '') + encodeURIComponent(value);
            const barcodeSrc = config.barcodeUrl
                ? config.barcodeUrl + '?code=' + encodeURIComponent(value)
                : '';

            const printWindow = window.open('', '_blank', 'width=480,height=640');

            if (!printWindow) {
                return;
            }

            printWindow.document.write(
                '<!DOCTYPE html><html><head><title>Shipping label</title>' +
                '<style>' +
                'body{font-family:Arial,sans-serif;padding:20px;text-align:center;}' +
                '.meta{margin-bottom:12px;font-size:12px;line-height:1.5;text-align:left;}' +
                '.meta strong{display:block;font-size:14px;margin-bottom:4px;}' +
                'img{display:block;margin:8px auto;}' +
                '.code{font-family:monospace;font-size:16px;font-weight:700;letter-spacing:0.06em;margin-top:8px;}' +
                '</style></head><body>' +
                '<div class="meta"><strong>' + (config.orderNumber || '') + '</strong>' +
                '<div>' + (config.customerName || '') + '</div>' +
                '<div>' + (config.address || '') + '</div></div>' +
                '<img src="' + qrSrc + '" width="180" height="180" alt="Tracking QR">' +
                (barcodeSrc ? '<img src="' + barcodeSrc + '" alt="Tracking barcode" style="max-width:100%;">' : '') +
                '<div class="code">' + value + '</div>' +
                '<script>window.onload=function(){window.print();};<\/script>' +
                '</body></html>'
            );
            printWindow.document.close();
        });
    }

    syncState();
})();
