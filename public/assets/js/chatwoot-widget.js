(function () {
    'use strict';

    var config = window.__MBS_CHATWOOT__;

    if (!config || !config.baseUrl || !config.websiteToken) {
        return;
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
        script.onerror = function () {
            console.warn('Chatwoot SDK could not be loaded from ' + config.baseUrl);
        };

        script.onload = function () {
            if (!window.chatwootSDK) {
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
                }
            }, 250);
        };

        firstScript.parentNode.insertBefore(script, firstScript);
    })(document, 'script');
})();
