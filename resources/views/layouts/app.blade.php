<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Lichtmoment – Hochzeitsfotografie')</title>
    <meta name="description" content="Hochzeitsfotografie mit Seele – Markus Knuth">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <meta property="og:title" content="@yield('og_title', 'Lichtmoment – Hochzeitsfotografie')">
    <meta property="og:description" content="@yield('og_description', 'Hochzeitsfotografie mit Seele – Markus Knuth')">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta name="twitter:card" content="summary_large_image">

    @vite(["resources/css/app.css", "resources/js/app.js"])

    {{-- Admin Design System --}}
    @if (isset($adminNav) && $adminNav)
        @include('admin.partials.design-system')
    @endif

    @stack('head')
</head>
<body class="text-gray-800 font-sans antialiased min-h-screen flex flex-col" style="background: transparent !important;">

    {{-- Navigation (for admin pages) --}}
    @if (isset($adminNav) && $adminNav)
    <nav class="fixed top-0 left-0 right-0 z-50 bg-white/90 backdrop-blur-xl border-b border-gray-100">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            <a href="{{ route('admin.dashboard') }}" class="font-serif text-lg sm:text-xl text-gold-400 tracking-wide shrink-0">Lichtmoment</a>
            <div class="flex items-center gap-2 sm:gap-3">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-ghost btn-sm rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                    <span class="hidden sm:inline">Dashboard</span>
                </a>
                <a href="{{ route('admin.logout') }}" class="btn btn-ghost btn-sm rounded-lg !text-red-400 hover:!text-red-600 hover:!bg-red-50">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    <span class="hidden sm:inline">Abmelden</span>
                </a>
            </div>
        </div>
    </nav>
    @endif

    <main class="flex-1">
        @yield('content')
    </main>

    {{-- Footer --}}
    @if (!isset($noFooter) || !$noFooter)
    <footer class="border-t border-gray-100/30 py-10" style="background: color-mix(in oklch, var(--color-offwhite) 90%, transparent)">
        <div class="max-w-6xl mx-auto px-6 text-center">
            <p class="font-serif text-2xl text-gold-400 mb-4">Lichtmoment</p>
            <div class="flex justify-center gap-6 text-xs uppercase tracking-widest text-gray-400 mb-4">
                <a href="{{ route('impressum') }}" class="hover:text-gold-400 transition-colors">Impressum</a>
                <a href="{{ route('datenschutz') }}" class="hover:text-gold-400 transition-colors">Datenschutz</a>
                <a href="{{ route('admin.login') }}" class="hover:text-gold-400 transition-colors">Admin</a>
            </div>
            <p class="text-xs text-gray-300">&copy; {{ date('Y') }} Lichtmoment – Hochzeitsfotografie</p>
        </div>
    </footer>
    @endif

    {{-- Toast notification --}}
    <script>
        // CSRF token for all AJAX requests
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content || '';

        window.getCsrfHeaders = function() {
            return { 'X-CSRF-TOKEN': CSRF_TOKEN };
        };

        window.showToast = function(msg, type = 'success') {
            const toast = document.createElement('div');
            toast.className = 'fixed bottom-6 right-6 z-[9999] px-6 py-3 rounded-xl text-sm font-medium shadow-lg transform translate-y-4 opacity-0 transition-all duration-300 ' +
                (type === 'error' ? 'bg-red-500 text-white' : 'bg-gold-400 text-white');
            toast.textContent = msg;
            document.body.appendChild(toast);
            requestAnimationFrame(() => { toast.classList.remove('translate-y-4', 'opacity-0'); });
            setTimeout(() => {
                toast.classList.add('translate-y-4', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        };
    </script>

    @stack('scripts')
</body>
</html>
