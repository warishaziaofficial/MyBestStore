<div
    x-show="loginOpen"
    x-cloak
    class="fixed inset-0 z-[70] flex items-center justify-center bg-slate-900/60 p-4 backdrop-blur-sm"
    @keydown.escape.window="loginOpen = false"
>
    <div
        @click.outside="loginOpen = false"
        class="w-full max-w-md rounded-2xl border border-border bg-white p-6 shadow-2xl"
    >
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-navy">Sign In</h2>
            <button type="button" @click="loginOpen = false" class="rounded-lg p-1 text-muted hover:bg-secondary">✕</button>
        </div>
        <p class="mt-1 text-sm text-muted">Welcome back to MyBestStore</p>

        <form class="mt-6 space-y-4" @submit.prevent="loginOpen = false">
            <div>
                <label class="text-sm font-medium text-foreground">Email</label>
                <input
                    type="email"
                    required
                    class="mbs-input mt-1"
                    placeholder="you@example.com"
                >
            </div>
            <div>
                <label class="text-sm font-medium text-foreground">Password</label>
                <input
                    type="password"
                    required
                    class="mbs-input mt-1"
                    placeholder="••••••••"
                >
            </div>
            <label class="flex items-center gap-2 text-sm text-muted">
                <input type="checkbox" class="rounded border-border text-blue-800 focus:ring-blue-800">
                Remember me
            </label>
            <button type="submit" class="mbs-btn mbs-btn-primary w-full">Sign In</button>
        </form>

        <p class="mt-4 text-center text-sm text-muted">
            New customer?
            <a href="#" class="font-semibold text-blue-800 hover:text-navy">Create account</a>
        </p>
    </div>
</div>
