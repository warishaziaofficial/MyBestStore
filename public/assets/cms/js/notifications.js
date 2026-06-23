(function () {
    var config = window.cmsNotifications;
    if (!config || !config.pollUrl) {
        return;
    }

    var POLL_MS = 12000;
    var TOAST_MS = 7000;
    var MAX_TOASTS = 4;

    var sinceId = config.sinceId || 0;
    var permissionRequested = false;
    var badge = document.getElementById('sf-notify-badge');
    var list = document.getElementById('sf-notify-list');
    var stack = document.getElementById('sf-toast-stack');

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function truncate(value, max) {
        var text = String(value || '');
        return text.length > max ? text.slice(0, max - 1) + '…' : text;
    }

    function formatCount(count) {
        return count > 99 ? '99+' : String(count);
    }

    function updateBadge(count) {
        if (!badge) {
            return;
        }

        badge.textContent = formatCount(count);
        badge.classList.toggle('is-empty', count <= 0);

        if (count > 0) {
            badge.classList.remove('is-pulse');
            void badge.offsetWidth;
            badge.classList.add('is-pulse');
        }
    }

    function typeIcon(type) {
        if (type === 'new_order') return '🛒';
        if (type === 'out_of_stock') return '📦';
        if (type === 'new_inquiry') return '✉️';
        return '🔔';
    }

    function renderList(items) {
        if (!list) {
            return;
        }

        if (!items.length) {
            list.innerHTML = '<p class="sf-notify-empty">No notifications yet.</p>';
            return;
        }

        list.innerHTML = items.map(function (item) {
            var href = item.link || config.indexUrl;
            var unreadClass = item.is_read ? '' : ' is-unread';

            return (
                '<a href="' + escapeHtml(href) + '" class="sf-notify-item' + unreadClass + '">' +
                    '<strong>' + escapeHtml(item.title) + '</strong>' +
                    '<span>' + escapeHtml(truncate(item.body, 60)) + '</span>' +
                '</a>'
            );
        }).join('');
    }

    function removeToast(node) {
        if (!node || !node.parentNode) {
            return;
        }

        node.classList.add('is-leaving');
        window.setTimeout(function () {
            node.remove();
        }, 260);
    }

    function showToast(item) {
        if (!stack) {
            return;
        }

        while (stack.children.length >= MAX_TOASTS) {
            stack.removeChild(stack.firstElementChild);
        }

        var href = item.link || config.indexUrl;
        var toast = document.createElement('div');
        toast.className = 'sf-toast';
        toast.innerHTML =
            '<div class="sf-toast-icon">' + typeIcon(item.type) + '</div>' +
            '<a href="' + escapeHtml(href) + '" class="sf-toast-body">' +
                '<strong>' + escapeHtml(item.title) + '</strong>' +
                '<span>' + escapeHtml(truncate(item.body, 90)) + '</span>' +
            '</a>' +
            '<button type="button" class="sf-toast-close" aria-label="Dismiss">×</button>';

        var closeBtn = toast.querySelector('.sf-toast-close');
        closeBtn.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopPropagation();
            removeToast(toast);
        });

        stack.appendChild(toast);
        window.requestAnimationFrame(function () {
            toast.classList.add('is-visible');
        });

        window.setTimeout(function () {
            removeToast(toast);
        }, TOAST_MS);
    }

    function maybeAskPermission() {
        if (permissionRequested || !('Notification' in window)) {
            return;
        }

        if (Notification.permission !== 'default') {
            permissionRequested = true;
            return;
        }

        permissionRequested = true;
        Notification.requestPermission().catch(function () {});
    }

    function showNativeNotification(item) {
        if (!('Notification' in window) || Notification.permission !== 'granted') {
            return;
        }

        var notification = new Notification(item.title, {
            body: item.body,
            icon: config.favicon || undefined,
            tag: 'cms-notification-' + item.id,
        });

        notification.onclick = function () {
            window.focus();
            window.location.href = item.link || config.indexUrl;
            notification.close();
        };
    }

    function handleNewItems(items) {
        if (!items.length) {
            return;
        }

        items.forEach(function (item) {
            if (document.hidden) {
                showNativeNotification(item);
            } else {
                showToast(item);
            }
        });

        if (document.hidden) {
            maybeAskPermission();
        }
    }

    function poll() {
        fetch(config.pollUrl + '?since_id=' + encodeURIComponent(sinceId), {
            credentials: 'same-origin',
            headers: { Accept: 'application/json' },
        })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('poll failed');
                }
                return response.json();
            })
            .then(function (data) {
                updateBadge(data.unread_count || 0);
                renderList(data.recent || []);

                if (Array.isArray(data.new) && data.new.length) {
                    handleNewItems(data.new);
                }

                if (typeof data.latest_id === 'number' && data.latest_id > sinceId) {
                    sinceId = data.latest_id;
                }
            })
            .catch(function () {});
    }

    document.addEventListener('click', maybeAskPermission, { once: true });

    updateBadge(parseInt(badge && badge.textContent, 10) || 0);
    window.setInterval(poll, POLL_MS);
    window.setTimeout(poll, 2500);
})();
