@php
    use App\Support\Mbs;

    $popupImage = is_file(public_path('assets/images/popup/newsletter-popup.jpg'))
        ? 'assets/images/popup/newsletter-popup.jpg'
        : 'assets/images/offers/offer-headphones.jpg';
@endphp

<div
    x-show="newsletterPopupOpen"
    x-cloak
    class="newsletter-popup-overlay"
    @keydown.escape.window="closeNewsletterPopup()"
    @click.self="closeNewsletterPopup()"
    role="dialog"
    aria-modal="true"
    aria-label="Newsletter subscription"
>
    <div class="newsletter-popup" @click.stop>
        <div class="newsletter-popup-grid">
            <div class="newsletter-popup-media">
                <img
                    src="{{ Mbs::image($popupImage) }}"
                    alt="Premium electronics and audio"
                    loading="lazy"
                >
            </div>

            <div class="newsletter-popup-content">
                <button type="button" class="newsletter-popup-close" @click="closeNewsletterPopup()" aria-label="Close popup">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-width="1.5" d="M6 18 18 6M6 6l12 12"/>
                    </svg>
                </button>

                <div class="newsletter-popup-body">
                    <h2 class="newsletter-popup-title">Get Exclusive Deals First</h2>
                    <p class="newsletter-popup-subtitle">Subscribe to receive premium electronics deals, new arrivals and special offers.</p>

                    <template x-if="!newsletterSuccess">
                        <form class="newsletter-popup-form" @submit="submitNewsletter">
                            <label for="newsletter-popup-email" class="sr-only">Email address</label>
                            <div class="newsletter-popup-input-wrap">
                                <svg class="newsletter-popup-input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/>
                                </svg>
                                <input
                                    id="newsletter-popup-email"
                                    type="email"
                                    x-model="newsletterEmail"
                                    class="newsletter-popup-input"
                                    placeholder="Your email address"
                                    autocomplete="email"
                                    required
                                >
                            </div>
                            <p class="newsletter-popup-error" x-show="newsletterError" x-text="newsletterError" x-cloak role="alert"></p>
                            <button type="submit" class="newsletter-popup-submit" :disabled="newsletterSubmitting">
                                <span x-show="!newsletterSubmitting">Subscribe</span>
                                <span x-show="newsletterSubmitting" x-cloak>Subscribing...</span>
                            </button>
                        </form>
                    </template>

                    <p class="newsletter-popup-success" x-show="newsletterSuccess" x-cloak role="status">
                        Thank you! You have subscribed successfully.
                    </p>

                    <p class="newsletter-popup-note">No spam. Only the best DigitalWares offers.</p>

                    <div class="newsletter-popup-social">
                        <a href="https://digitalwares.pk/" target="_blank" rel="noopener" class="newsletter-popup-social-link" aria-label="X (Twitter)">
                            <svg fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                            </svg>
                        </a>
                        <a href="https://digitalwares.pk/" target="_blank" rel="noopener" class="newsletter-popup-social-link" aria-label="Facebook">
                            <svg fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M14 8h2V5h-2c-2.2 0-4 1.8-4 4v2H8v3h2v7h3v-7h2.5L16 12h-3v-2c0-.6.4-1 1-1z"/>
                            </svg>
                        </a>
                        <a href="https://digitalwares.pk/" target="_blank" rel="noopener" class="newsletter-popup-social-link" aria-label="Instagram">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <rect x="4" y="4" width="16" height="16" rx="4" stroke-width="1.75"/>
                                <circle cx="12" cy="12" r="3.5" stroke-width="1.75"/>
                                <circle cx="17.25" cy="6.75" r="0.75" fill="currentColor" stroke="none"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
