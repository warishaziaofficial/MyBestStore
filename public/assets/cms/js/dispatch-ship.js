(function () {
    const courier = document.getElementById('courier_key');
    const tracking = document.getElementById('tracking_number');
    const confirmBtn = document.getElementById('dp-confirm-dispatch');
    const summary = document.getElementById('dp-tracking-summary');
    const autoBtn = document.getElementById('dp-auto-tracking');

    if (!courier || !tracking || !confirmBtn) {
        return;
    }

    function courierLabel() {
        const option = courier.options[courier.selectedIndex];
        return option && option.value ? option.textContent : '';
    }

    function syncState() {
        const hasCourier = Boolean(courier.value);
        tracking.disabled = !hasCourier;

        if (!hasCourier) {
            tracking.value = '';
        }

        const ready = hasCourier && tracking.value.trim() !== '';
        confirmBtn.disabled = !ready;

        if (ready && summary) {
            summary.hidden = false;
            summary.textContent = courierLabel() + ' · ' + tracking.value.trim();
        } else if (summary) {
            summary.hidden = true;
            summary.textContent = '';
        }
    }

    courier.addEventListener('change', syncState);
    tracking.addEventListener('input', syncState);

    if (autoBtn) {
        autoBtn.addEventListener('click', () => {
            if (!courier.value) {
                courier.focus();
                return;
            }

            tracking.value = String(Math.floor(1000000 + Math.random() * 9000000));
            tracking.disabled = false;
            syncState();
        });
    }

    syncState();
})();
