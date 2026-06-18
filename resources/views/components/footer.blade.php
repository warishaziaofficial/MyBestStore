<footer class="mbs-footer">
    <div class="mbs-container mbs-footer-inner">
        <div class="grid gap-10 md:grid-cols-2 lg:grid-cols-4">
            <div>
                <a href="{{ route('home') }}" class="mbs-footer-logo footer-logo">
                    <img src="{{ asset('logo.png') }}" alt="MyBestStore.pk">
                </a>
                <p class="mt-4 max-w-xs text-sm leading-relaxed">
                    The premium e-commerce destination in Pakistan. Quality electronics, appliances and audio with nationwide support.
                </p>
                <div class="mt-6 flex gap-3">
                    <a href="https://mybeststore.pk/" target="_blank" rel="noopener" class="mbs-icon-btn border-blue-800 bg-blue-950 text-blue-100" aria-label="Website">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" stroke-width="2"/><path stroke-linecap="round" stroke-width="2" d="M3 12h18M12 3a15 15 0 0 1 0 18M12 3a15 15 0 0 0 0 18"/></svg>
                    </a>
                    <a href="#" class="mbs-icon-btn border-blue-800 bg-blue-950 text-blue-100" aria-label="Instagram">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="4" y="4" width="16" height="16" rx="4" stroke-width="2"/><circle cx="12" cy="12" r="3.5" stroke-width="2"/></svg>
                    </a>
                    <a href="#" class="mbs-icon-btn border-blue-800 bg-blue-950 text-blue-100" aria-label="Facebook">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M14 8h2V5h-2c-2.2 0-4 1.8-4 4v2H8v3h2v7h3v-7h2.5L16 12h-3v-2c0-.6.4-1 1-1z"/></svg>
                    </a>
                </div>
                <div class="mbs-payment-row">
                    <span class="mbs-payment-badge">VISA</span>
                    <span class="mbs-payment-badge">MC</span>
                    <span class="mbs-payment-badge">EASYPAISA</span>
                    <span class="mbs-payment-badge">JAZZCASH</span>
                </div>
            </div>
            <div>
                <h4 class="mbs-footer-heading">Shop</h4>
                <ul class="mbs-footer-links">
                    <li><a href="{{ route('shop') }}">All Products</a></li>
                    <li><a href="{{ route('new-arrivals') }}">New Arrivals</a></li>
                    <li><a href="{{ route('categories') }}">Categories</a></li>
                    <li><a href="{{ route('shop') }}#deals">Deals & Offers</a></li>
                </ul>
            </div>
            <div>
                <h4 class="mbs-footer-heading">Support</h4>
                <ul class="mbs-footer-links">
                    <li><a href="{{ route('contact') }}">Contact Us</a></li>
                    <li><a href="{{ route('blog') }}">Blog & Guides</a></li>
                    <li><a href="#">Warranty</a></li>
                    <li><a href="#">Returns & Exchanges</a></li>
                </ul>
            </div>
            <div>
                <h4 class="mbs-footer-heading">Newsletter</h4>
                <p class="mt-4 text-sm">Subscribe for special offers, new arrivals and exclusive deals.</p>
                <form class="mt-4 flex gap-2" @submit.prevent>
                    <input type="email" placeholder="Your email" class="mbs-footer-input">
                    <button type="submit" class="mbs-btn mbs-btn-primary shrink-0">Join</button>
                </form>
            </div>
        </div>
        <div class="mbs-footer-bottom">
            <p>© {{ date('Y') }} MyBestStore.pk — All rights reserved.</p>
            <div class="flex gap-6">
                <a href="#" class="hover:text-white">Privacy</a>
                <a href="#" class="hover:text-white">Terms</a>
                <a href="#" class="hover:text-white">Cookies</a>
            </div>
        </div>
    </div>
</footer>
