(function () {
    'use strict';

    var config = window.__MBS_CHATWOOT__;

    if (!config || !config.baseUrl) {
        return;
    }

    if (!config.websiteToken && !config.fallbackEnabled) {
        return;
    }

    var fallbackShown = false;

    function showFallback() {
        if (fallbackShown || !config.fallbackEnabled || !config.fallbackUrl) {
            return;
        }

        fallbackShown = true;

        var button = document.createElement('a');
        button.href = config.fallbackUrl;
        button.className = 'mbs-chat-fallback';
        button.setAttribute('aria-label', config.launcherTitle || 'Chat with us');
        button.innerHTML = '<span class="mbs-chat-fallback__icon" aria-hidden="true">💬</span><span class="mbs-chat-fallback__label">' + (config.launcherTitle || 'Chat with us') + '</span>';

        document.body.appendChild(button);
    }

    function identifyUser(customer) {
        if (!window.$chatwoot || !customer || !customer.email) {
            return;
        }

        try {
            window.$chatwoot.setUser(customer.email, {
                email: customer.email,
                name: customer.name || customer.email,
            });

            window.$chatwoot.setCustomAttributes({
                ecommerce_store: config.storeName || 'MyBestStore',
                customer_source: 'storefront',
            });
        } catch (error) {
            console.warn('Chatwoot identify failed', error);
        }
    }

    function resetUser() {
        if (window.$chatwoot && typeof window.$chatwoot.reset === 'function') {
            window.$chatwoot.reset();
        }
    }

    function setPageContext() {
        if (!window.$chatwoot || typeof window.$chatwoot.setConversationCustomAttributes !== 'function') {
            return;
        }

        try {
            window.$chatwoot.setConversationCustomAttributes({
                current_page: window.location.pathname,
                page_title: document.title,
                store_url: window.location.origin,
            });
        } catch (error) {
            console.warn('Chatwoot page context failed', error);
        }
    }

    function onReady() {
        if (config.customer) {
            identifyUser(config.customer);
        }

        setPageContext();
    }

    window.MbsChatwoot = {
        identifyUser: identifyUser,
        resetUser: resetUser,
        setPageContext: setPageContext,
    };

    (function loadSdk(documentRef, tagName) {
        var script = documentRef.createElement(tagName);
        var firstScript = documentRef.getElementsByTagName(tagName)[0];

        script.src = config.baseUrl + '/packs/js/sdk.js';
        script.async = true;

        if (!config.websiteToken) {
            showFallback();
            return;
        }

        script.onerror = function () {
            console.warn('Chatwoot SDK could not be loaded from ' + config.baseUrl);
            showFallback();
        };

        script.onload = function () {
            if (!window.chatwootSDK) {
                showFallback();
                return;
            }

            window.chatwootSDK.run({
                websiteToken: config.websiteToken,
                baseUrl: config.baseUrl,
            });

            var attempts = 0;
            var readyTimer = window.setInterval(function () {
                attempts += 1;

                if (window.$chatwoot) {
                    window.clearInterval(readyTimer);
                    onReady();
                    return;
                }

                if (attempts >= 40) {
                    window.clearInterval(readyTimer);
                    showFallback();
                }
            }, 250);
        };

        window.setTimeout(function () {
            if (!window.$chatwoot && !fallbackShown) {
                showFallback();
            }
        }, 8000);

        firstScript.parentNode.insertBefore(script, firstScript);
    })(document, 'script');
})();
