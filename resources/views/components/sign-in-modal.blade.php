<div
    x-show="loginOpen"
    x-cloak
    class="auth-modal-overlay"
    @keydown.escape.window="closeAuth()"
>
    <div
        class="auth-modal"
        role="dialog"
        aria-modal="true"
        :aria-label="authView === 'register' ? 'Create account' : authView === 'forgot' ? 'Forgot password' : authView === 'track' ? 'Track order' : 'Sign in'"
        @click.outside="closeAuth()"
    >
        <button type="button" class="auth-modal-close" @click="closeAuth()" aria-label="Close">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"/>
            </svg>
        </button>

        <div class="auth-modal-body">
            <p class="auth-modal-message auth-modal-message--error" x-show="authError" x-text="authError" x-cloak role="alert"></p>
            <p class="auth-modal-message auth-modal-message--success" x-show="authSuccess" x-text="authSuccess" x-cloak role="status"></p>

            {{-- Signed in state --}}
            <div x-show="customer && authView === 'signin'" x-cloak class="auth-modal-panel">
                <div class="auth-modal-head">
                    <h2 class="auth-modal-title">My Account</h2>
                    <p class="auth-modal-subtitle">Signed in as <strong x-text="customer?.name"></strong></p>
                </div>
                <p class="auth-modal-note" x-text="customer?.email"></p>
                <div class="auth-modal-actions">
                    <button type="button" class="mbs-btn mbs-btn-primary auth-modal-btn" @click="closeAuth()">Continue Shopping</button>
                    <button type="button" class="auth-modal-text-btn" @click="logoutCustomer()">Sign Out</button>
                </div>
            </div>

            {{-- Sign In --}}
            <div x-show="!customer && authView === 'signin'" class="auth-modal-panel">
                <div class="auth-modal-head">
                    <h2 class="auth-modal-title">Sign In</h2>
                    <p class="auth-modal-subtitle">Welcome back to MyBestStore</p>
                </div>

                <form class="auth-modal-form" @submit="submitLogin">
                    <div class="auth-modal-field">
                        <label for="auth-login-email">Email</label>
                        <input id="auth-login-email" name="email" type="email" class="mbs-input" placeholder="you@example.com" autocomplete="email">
                    </div>

                    <div class="auth-modal-field">
                        <div class="auth-modal-field-label-row">
                            <label for="auth-login-password">Password</label>
                            <button type="button" class="auth-modal-link-btn" @click="openAuth('forgot')">Forgot Password?</button>
                        </div>
                        <div class="auth-modal-password-wrap">
                            <input
                                id="auth-login-password"
                                name="password"
                                :type="showLoginPassword ? 'text' : 'password'"
                                class="mbs-input auth-modal-password-input"
                                placeholder="Your password"
                                autocomplete="current-password"
                            >
                            <button
                                type="button"
                                class="auth-modal-password-toggle"
                                @click="showLoginPassword = !showLoginPassword"
                                :aria-label="showLoginPassword ? 'Hide password' : 'Show password'"
                            >
                                <svg x-show="!showLoginPassword" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M2.036 12.322a1 1 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                </svg>
                                <svg x-show="showLoginPassword" x-cloak fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <label class="auth-modal-checkbox">
                        <input type="checkbox" name="remember" value="1">
                        <span>Remember me</span>
                    </label>

                    <button type="submit" class="mbs-btn mbs-btn-primary auth-modal-btn" :disabled="authSubmitting">
                        <span x-show="!authSubmitting">Sign In</span>
                        <span x-show="authSubmitting" x-cloak>Signing in...</span>
                    </button>
                </form>

                <div class="auth-modal-footer-links">
                    <button type="button" class="auth-modal-text-btn" @click="openAuth('register')">Create Account</button>
                    <span class="auth-modal-footer-sep" aria-hidden="true">·</span>
                    <button type="button" class="auth-modal-text-btn" @click="continueAsGuest()">Continue as Guest</button>
                    <span class="auth-modal-footer-sep" aria-hidden="true">·</span>
                    <button type="button" class="auth-modal-text-btn" @click="openAuth('track')">Track Order</button>
                </div>
            </div>

            {{-- Create Account --}}
            <div x-show="authView === 'register'" x-cloak class="auth-modal-panel">
                <div class="auth-modal-head">
                    <h2 class="auth-modal-title">Create Account</h2>
                    <p class="auth-modal-subtitle">Join MyBestStore for faster checkout and order tracking</p>
                </div>

                <form class="auth-modal-form" @submit="submitRegister">
                    <div class="auth-modal-field">
                        <label for="auth-register-name">Full Name</label>
                        <input id="auth-register-name" name="name" type="text" class="mbs-input" placeholder="Your full name" autocomplete="name">
                    </div>

                    <div class="auth-modal-field">
                        <label for="auth-register-email">Email</label>
                        <input id="auth-register-email" name="email" type="email" class="mbs-input" placeholder="you@example.com" autocomplete="email">
                    </div>

                    <div class="auth-modal-field">
                        <label for="auth-register-phone">Phone Number</label>
                        <input id="auth-register-phone" name="phone" type="tel" class="mbs-input" placeholder="+92 300 1234567" autocomplete="tel">
                    </div>

                    <div class="auth-modal-field">
                        <label for="auth-register-password">Password</label>
                        <div class="auth-modal-password-wrap">
                            <input
                                id="auth-register-password"
                                name="password"
                                :type="showRegisterPassword ? 'text' : 'password'"
                                class="mbs-input auth-modal-password-input"
                                placeholder="At least 8 characters"
                                autocomplete="new-password"
                            >
                            <button
                                type="button"
                                class="auth-modal-password-toggle"
                                @click="showRegisterPassword = !showRegisterPassword"
                                :aria-label="showRegisterPassword ? 'Hide password' : 'Show password'"
                            >
                                <svg x-show="!showRegisterPassword" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M2.036 12.322a1 1 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                </svg>
                                <svg x-show="showRegisterPassword" x-cloak fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="auth-modal-field">
                        <label for="auth-register-password-confirm">Confirm Password</label>
                        <div class="auth-modal-password-wrap">
                            <input
                                id="auth-register-password-confirm"
                                name="password_confirmation"
                                :type="showRegisterPasswordConfirm ? 'text' : 'password'"
                                class="mbs-input auth-modal-password-input"
                                placeholder="Repeat your password"
                                autocomplete="new-password"
                            >
                            <button
                                type="button"
                                class="auth-modal-password-toggle"
                                @click="showRegisterPasswordConfirm = !showRegisterPasswordConfirm"
                                :aria-label="showRegisterPasswordConfirm ? 'Hide password' : 'Show password'"
                            >
                                <svg x-show="!showRegisterPasswordConfirm" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M2.036 12.322a1 1 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                </svg>
                                <svg x-show="showRegisterPasswordConfirm" x-cloak fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="mbs-btn mbs-btn-primary auth-modal-btn" :disabled="authSubmitting">
                        <span x-show="!authSubmitting">Create Account</span>
                        <span x-show="authSubmitting" x-cloak>Creating account...</span>
                    </button>
                </form>

                <p class="auth-modal-switch">
                    Already have an account?
                    <button type="button" class="auth-modal-link-btn" @click="openAuth('signin')">Sign In</button>
                </p>
            </div>

            {{-- Forgot Password --}}
            <div x-show="authView === 'forgot'" x-cloak class="auth-modal-panel">
                <div class="auth-modal-head">
                    <h2 class="auth-modal-title">Forgot Password</h2>
                    <p class="auth-modal-subtitle">Enter your email and we will send reset instructions if an account exists.</p>
                </div>

                <form class="auth-modal-form" @submit="submitForgotPassword">
                    <div class="auth-modal-field">
                        <label for="auth-forgot-email">Email</label>
                        <input id="auth-forgot-email" name="email" type="email" class="mbs-input" placeholder="you@example.com" autocomplete="email">
                    </div>

                    <button type="submit" class="mbs-btn mbs-btn-primary auth-modal-btn" :disabled="authSubmitting">
                        <span x-show="!authSubmitting">Send Reset Link</span>
                        <span x-show="authSubmitting" x-cloak>Sending...</span>
                    </button>
                </form>

                <p class="auth-modal-note">If this email exists, reset instructions will be sent.</p>

                <p class="auth-modal-switch">
                    <button type="button" class="auth-modal-link-btn" @click="openAuth('signin')">Back to Sign In</button>
                </p>
            </div>

            {{-- Track Order --}}
            <div x-show="authView === 'track'" x-cloak class="auth-modal-panel">
                <div class="auth-modal-head">
                    <h2 class="auth-modal-title">Track Order</h2>
                    <p class="auth-modal-subtitle">Enter your order number and phone or email to view order status.</p>
                </div>

                <form class="auth-modal-form" @submit="submitTrackOrder">
                    <div class="auth-modal-field">
                        <label for="auth-track-order-number">Order Number or Barcode</label>
                        <input id="auth-track-order-number" name="order_number" type="text" class="mbs-input" placeholder="e.g. MBS-ORD-00014">
                    </div>

                    <div class="auth-modal-field">
                        <label for="auth-track-phone">Phone Number</label>
                        <input id="auth-track-phone" name="phone" type="text" class="mbs-input" placeholder="03XX XXXXXXX">
                    </div>

                    <div class="auth-modal-field">
                        <label for="auth-track-email">Email (optional if phone entered)</label>
                        <input id="auth-track-email" name="email" type="email" class="mbs-input" placeholder="you@example.com" autocomplete="email">
                    </div>

                    <button type="submit" class="mbs-btn mbs-btn-primary auth-modal-btn" :disabled="authSubmitting">
                        <span x-show="!authSubmitting">Track Order</span>
                        <span x-show="authSubmitting" x-cloak>Searching...</span>
                    </button>
                </form>

                <p class="auth-modal-switch">
                    <button type="button" class="auth-modal-link-btn" @click="openAuth('signin')">Back to Sign In</button>
                </p>
            </div>
        </div>
    </div>
</div>
