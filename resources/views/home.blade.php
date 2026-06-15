@extends('layouts.app')

@section('title', 'Lichtmoment – Hochzeitsfotografie')

@section('content')
<!-- Hero with Three.js -->
<section class="relative min-h-screen flex items-center justify-center overflow-hidden bg-gradient-to-b from-offwhite to-cream">
    <canvas id="hero-canvas" class="absolute inset-0 w-full h-full z-0"></canvas>
    <div class="relative z-10 text-center px-4 sm:px-6">
        <h1 class="font-serif text-3xl sm:text-6xl md:text-8xl font-light text-gold-400 tracking-[0.08em] mb-4" style="text-shadow: 0 2px 40px rgba(201,169,78,0.15)">
            Lichtmoment
        </h1>
        <p class="text-gray-400 text-base sm:text-lg tracking-[0.08em] font-light">Hochzeitsfotografie mit Seele</p>
    </div>
</section>

{{-- Portfolio --}}
@if($portfolioPhotos->isNotEmpty())
<section class="py-16 sm:py-24 px-4 sm:px-6 max-w-6xl mx-auto" id="portfolio">
    <h2 class="font-serif text-3xl sm:text-4xl font-light text-center mb-8 sm:mb-12 text-gray-700">Portfolio</h2>
    <div class="columns-1 md:columns-2 lg:columns-3 gap-4 sm:gap-6">
        @foreach($portfolioPhotos as $photo)
        @php
            $imgSrc = str_starts_with($photo->filename, 'portfolio/') ? '/storage/'.$photo->filename : $photo->filename;
        @endphp
        <div class="break-inside-avoid mb-4 sm:mb-6 rounded-xl overflow-hidden shadow-sm bg-white hover:shadow-lg hover:-translate-y-1 transition-all duration-300 cursor-pointer portfolio-item"
             onclick="openPortfolioLightbox({{ $loop->index }})">
            <img src="{{ $imgSrc }}" alt="{{ $photo->original_name }}" class="w-full" loading="lazy">
        </div>
        @endforeach
    </div>
</section>

{{-- Portfolio Lightbox --}}
<div id="portfolio-lightbox" class="fixed inset-0 z-[9999] bg-black/95 hidden items-center justify-center" aria-hidden="true" onclick="if(event.target===this) closePortfolioLightbox()" style="touch-action: none;">
    <button class="absolute top-3 right-3 sm:top-4 sm:right-4 w-10 h-10 flex items-center justify-center text-white/70 hover:text-white hover:bg-white/10 rounded-xl transition-colors z-20" onclick="closePortfolioLightbox()">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
    <div class="absolute top-3 left-3 sm:top-4 sm:left-4 text-white/50 text-xs font-mono z-20" id="portfolio-lb-counter"></div>
    <button class="absolute left-1 sm:left-3 top-1/2 -translate-y-1/2 w-12 h-12 sm:w-14 sm:h-14 flex items-center justify-center text-white/50 hover:text-white hover:bg-white/10 rounded-xl transition-colors z-20" onclick="portfolioLbPrev()">
        <svg class="w-7 h-7 sm:w-9 sm:h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </button>
    <img id="portfolio-lb-img" src="" alt="" class="max-w-[90vw] max-h-[85vh] sm:max-w-[85vw] sm:max-h-[80vh] object-contain rounded-lg select-none" draggable="false">
    <button class="absolute right-1 sm:right-3 top-1/2 -translate-y-1/2 w-12 h-12 sm:w-14 sm:h-14 flex items-center justify-center text-white/50 hover:text-white hover:bg-white/10 rounded-xl transition-colors z-20" onclick="portfolioLbNext()">
        <svg class="w-7 h-7 sm:w-9 sm:h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    </button>
</div>
@endif

<!-- About / Photographer -->
<section class="py-16 sm:py-24 px-4 sm:px-6 bg-white" id="about">
    <div class="max-w-2xl mx-auto text-center">
        <div class="w-32 h-32 sm:w-40 sm:h-40 mx-auto mb-6 sm:mb-8 rounded-full bg-gradient-to-br from-gold-200 to-gold-400 p-1 shadow-lg shadow-gold-200/30">
            <div class="w-full h-full rounded-full bg-gradient-to-br from-cream to-gold-50 flex items-center justify-center">
                <svg class="w-12 h-12 sm:w-16 sm:h-16 text-gold-300" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                </svg>
            </div>
        </div>
        <h2 class="font-serif text-2xl sm:text-3xl font-normal mb-4 sm:mb-6 text-gray-700">{{ $photographer['name'] }}</h2>
        <div class="space-y-4 text-gray-500 font-light leading-relaxed max-w-lg mx-auto text-sm sm:text-base">
            @foreach($photographer['bio'] as $paragraph)
            <p>{{ $paragraph }}</p>
            @endforeach
        </div>
        <div class="mt-6 sm:mt-8 flex flex-col sm:flex-row items-center justify-center gap-3 sm:gap-4">
            <a href="tel:{{ $photographer['phone'] }}" class="inline-flex items-center gap-2 px-5 py-2.5 border border-gray-200 rounded-xl text-gray-600 hover:border-gold-400 hover:text-gold-500 transition-all text-sm">
                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg>
                {{ $photographer['phone'] }}
            </a>
            <a href="mailto:{{ $photographer['email'] }}" class="inline-flex items-center gap-2 px-5 py-2.5 border border-gray-200 rounded-xl text-gray-600 hover:border-gold-400 hover:text-gold-500 transition-all text-sm">
                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
                {{ $photographer['email'] }}
            </a>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    // === PORTFOLIO LIGHTBOX ===
    const PORTFOLIO_PHOTOS = @json($portfolioPhotos->map(fn($p) => [
        'src' => str_starts_with($p->filename, 'portfolio/') ? '/storage/'.$p->filename : $p->filename,
    ]));
    let portfolioLbIndex = 0;
    let portfolioTouchStartX = 0;
    let portfolioTouchStartY = 0;

    function openPortfolioLightbox(index) {
        portfolioLbIndex = index;
        updatePortfolioLightbox();
        const lb = document.getElementById('portfolio-lightbox');
        lb.classList.remove('hidden');
        lb.classList.add('flex');
        lb.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        document.body.style.position = 'fixed';
        document.body.style.width = '100%';
        window.__portfolioLbHistoryPushed = true;
        history.pushState({ portfolioLb: true }, '');
        history.pushState({ portfolioLb: true }, '');
    }

    function closePortfolioLightbox() {
        const lb = document.getElementById('portfolio-lightbox');
        lb.classList.add('hidden');
        lb.classList.remove('flex');
        lb.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
        document.body.style.position = '';
        document.body.style.width = '';
        if (window.__portfolioLbHistoryPushed) {
            window.__portfolioLbHistoryPushed = false;
            history.go(-2);
        }
    }

    function updatePortfolioLightbox() {
        document.getElementById('portfolio-lb-img').src = PORTFOLIO_PHOTOS[portfolioLbIndex].src;
        const counter = document.getElementById('portfolio-lb-counter');
        if (counter) counter.textContent = (portfolioLbIndex + 1) + ' / ' + PORTFOLIO_PHOTOS.length;
    }

    function portfolioLbPrev() {
        portfolioLbIndex = (portfolioLbIndex - 1 + PORTFOLIO_PHOTOS.length) % PORTFOLIO_PHOTOS.length;
        updatePortfolioLightbox();
    }

    function portfolioLbNext() {
        portfolioLbIndex = (portfolioLbIndex + 1) % PORTFOLIO_PHOTOS.length;
        updatePortfolioLightbox();
    }

    // Browser back / swipe-back
    window.addEventListener('popstate', function(e) {
        const lb = document.getElementById('portfolio-lightbox');
        if (lb && !lb.classList.contains('hidden') && window.__portfolioLbHistoryPushed) {
            window.__portfolioLbHistoryPushed = false;
            history.pushState({ portfolioLb: true }, '');
            lb.classList.add('hidden');
            lb.classList.remove('flex');
            lb.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
            document.body.style.position = '';
            document.body.style.width = '';
        }
    });

    // Touch swipe
    const portfolioLbEl = document.getElementById('portfolio-lightbox');
    portfolioLbEl?.addEventListener('touchstart', (e) => {
        portfolioTouchStartX = e.touches[0].clientX;
        portfolioTouchStartY = e.touches[0].clientY;
    }, { passive: true });
    portfolioLbEl?.addEventListener('touchmove', (e) => { e.preventDefault(); }, { passive: false });
    portfolioLbEl?.addEventListener('touchend', (e) => {
        const dx = e.changedTouches[0].clientX - portfolioTouchStartX;
        const dy = e.changedTouches[0].clientY - portfolioTouchStartY;
        if (Math.abs(dx) > Math.abs(dy) && Math.abs(dx) > 50) {
            if (dx < 0) portfolioLbNext();
            else portfolioLbPrev();
        }
    }, { passive: true });

    // Keyboard
    document.addEventListener('keydown', (e) => {
        const lb = document.getElementById('portfolio-lightbox');
        if (lb.classList.contains('hidden')) return;
        if (e.key === 'Escape') closePortfolioLightbox();
        if (e.key === 'ArrowLeft') portfolioLbPrev();
        if (e.key === 'ArrowRight') portfolioLbNext();
    });
</script>
@endpush
