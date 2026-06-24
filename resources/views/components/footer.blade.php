<footer class="site-footer">
    <div class="mbs-container site-footer-inner">
        <div class="footer-main">
            <div class="footer-brand">
                <a href="{{ route('home') }}" class="footer-logo" aria-label="DigitalWares.pk home">
                    <img src="{{ asset('assets/images/logo/logo-icon.png') }}" alt="" class="footer-logo-icon" aria-hidden="true">
                    <span class="footer-logo-text">
                        <span class="footer-logo-name">digitalwares<sup class="footer-logo-mark">®</sup></span>
                        <span class="footer-logo-tagline">complete hardware solutions</span>
                    </span>
                </a>

                <h2 class="footer-brand-heading">
                    Keep up with our latest
                    <span class="footer-brand-heading-accent">deals and promotions</span>
                </h2>

                <form class="footer-newsletter" @submit.prevent>
                    <label for="footer-newsletter-email" class="sr-only">Email address</label>
                    <input
                        id="footer-newsletter-email"
                        type="email"
                        placeholder="Your email address"
                        class="footer-newsletter-input"
                    >
                    <button type="submit" class="footer-newsletter-submit" aria-label="Subscribe to newsletter">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5"/>
                        </svg>
                    </button>
                </form>

                <div class="footer-social">
                    <a href="https://digitalwares.pk/" target="_blank" rel="noopener" class="footer-social-link" aria-label="DigitalWares website">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <circle cx="12" cy="12" r="9" stroke-width="1.75"/>
                            <path stroke-linecap="round" stroke-width="1.75" d="M3 12h18M12 3a15 15 0 0 1 0 18M12 3a15 15 0 0 0 0 18"/>
                        </svg>
                    </a>
                    <a href="https://digitalwares.pk/" target="_blank" rel="noopener" class="footer-social-link" aria-label="X (Twitter)">
                        <svg fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                        </svg>
                    </a>
                    <a href="https://digitalwares.pk/" target="_blank" rel="noopener" class="footer-social-link" aria-label="Facebook">
                        <svg fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M14 8h2V5h-2c-2.2 0-4 1.8-4 4v2H8v3h2v7h3v-7h2.5L16 12h-3v-2c0-.6.4-1 1-1z"/>
                        </svg>
                    </a>
                    <a href="https://digitalwares.pk/" target="_blank" rel="noopener" class="footer-social-link" aria-label="Instagram">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <rect x="4" y="4" width="16" height="16" rx="4" stroke-width="1.75"/>
                            <circle cx="12" cy="12" r="3.5" stroke-width="1.75"/>
                            <circle cx="17.25" cy="6.75" r="0.75" fill="currentColor" stroke="none"/>
                        </svg>
                    </a>
                </div>
            </div>

            <div class="footer-column">
                <h3 class="footer-column-title">About</h3>
                <ul class="footer-links">
                    <li><a href="{{ route('about-us') }}">About Us</a></li>
                    <li><a href="{{ route('our-story') }}">Our Story</a></li>
                    <li><a href="{{ route('product-guides') }}">Product Guides</a></li>
                    <li><a href="{{ route('blog') }}">Blog</a></li>
                    <li><a href="{{ route('contact') }}">Contact Us</a></li>
                </ul>
            </div>

            <div class="footer-column">
                <h3 class="footer-column-title">Information</h3>
                <ul class="footer-links">
                    <li><a href="{{ route('terms-of-service') }}">Terms Of Service</a></li>
                    <li><a href="{{ route('privacy-policy') }}">Privacy Policy</a></li>
                    <li><a href="{{ route('return-policy') }}">Return Policy</a></li>
                    <li><a href="{{ route('warranty-policy') }}">Warranty Policy</a></li>
                    <li><a href="{{ route('faq') }}">FAQ</a></li>
                </ul>
            </div>

            <div class="footer-column">
                <h3 class="footer-column-title">Support</h3>
                <ul class="footer-links">
                    <li><button type="button" @click="loginOpen = true">Sign In</button></li>
                    <li><button type="button" @click="loginOpen = true">Create Account</button></li>
                    <li><a href="{{ route('wishlist') }}">Wishlist</a></li>
                    <li><a href="{{ route('compare') }}">Compare Products</a></li>
                    <li><a href="{{ route('contact') }}">Track Order</a></li>
                    <li><a href="{{ route('contact') }}">Help Center</a></li>
                    <li><a href="{{ route('contact') }}">Live Chat</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p class="footer-copyright">© 2026 DigitalWares.pk. All rights reserved.</p>
            <x-payment-methods-strip variant="footer" class="footer-payments" />
        </div>
    </div>
</footer>
