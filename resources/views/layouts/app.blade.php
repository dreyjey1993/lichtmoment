<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Lichtmoment – Hochzeitsfotografie')</title>
    <meta name="description" content="Hochzeitsfotografie mit Seele – Markus Knuth">

    @vite(["resources/css/app.css", "resources/js/app.js"])

    @stack('head')
</head>
<body class="bg-offwhite text-gray-800 font-sans antialiased min-h-screen flex flex-col">

    {{-- Navigation (for admin pages) --}}
    @if (isset($adminNav) && $adminNav)
    <nav class="fixed top-0 left-0 right-0 z-50 bg-white/90 backdrop-blur-xl border-b border-gray-100">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            <a href="{{ route('admin.dashboard') }}" class="font-serif text-lg sm:text-xl text-gold-400 tracking-wide shrink-0">Lichtmoment</a>
            <div class="flex items-center gap-3 sm:gap-6 text-xs sm:text-sm">
                <a href="{{ route('admin.dashboard') }}" class="text-gray-500 hover:text-gold-400 transition-colors">Dashboard</a>
                <a href="{{ route('admin.logout') }}" class="text-gray-400 hover:text-red-500 transition-colors">Abmelden</a>
            </div>
        </div>
    </nav>
    @endif

    <main class="flex-1">
        @yield('content')
    </main>

    {{-- Footer --}}
    @if (!isset($noFooter) || !$noFooter)
    <footer class="bg-cream border-t border-gray-100 py-10">
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
