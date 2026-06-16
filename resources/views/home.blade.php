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
    <button class="absolute top-3 right-3 sm:top-4 sm:right-4 w-10 h-10 flex items-center justify-center text-white/70 hover:text-white hover:bg-white/10 rounded-xl transition-colors z-[9999]" onclick="closePortfolioLightbox()">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
    <div class="absolute top-3 left-3 sm:top-4 sm:left-4 text-white/50 text-xs font-mono z-[9999]" id="portfolio-lb-counter"></div>
    <button class="absolute left-1 sm:left-3 top-1/2 -translate-y-1/2 w-12 h-12 sm:w-14 sm:h-14 flex items-center justify-center text-white/50 hover:text-white hover:bg-white/10 rounded-xl transition-colors z-[9999]" onclick="portfolioLbPrev()">
        <svg class="w-7 h-7 sm:w-9 sm:h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </button>
    <div class="absolute inset-0 flex items-center justify-center overflow-hidden z-[1]" id="portfolio-lb-pan-container">
        <img id="portfolio-lb-img" src="" alt="" class="max-w-[90vw] max-h-[85vh] sm:max-w-[85vw] sm:max-h-[80vh] object-contain rounded-lg select-none" draggable="false">
    </div>
    <button class="absolute right-1 sm:right-3 top-1/2 -translate-y-1/2 w-12 h-12 sm:w-14 sm:h-14 flex items-center justify-center text-white/50 hover:text-white hover:bg-white/10 rounded-xl transition-colors z-[9999]" onclick="portfolioLbNext()">
        <svg class="w-7 h-7 sm:w-9 sm:h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    </button>
    <div id="portfolio-lb-zoom-indicator" class="absolute bottom-4 left-1/2 -translate-x-1/2 bg-black/60 text-white text-xs px-3 py-1.5 rounded-full opacity-0 transition-opacity duration-300 pointer-events-none z-[9999] font-mono"></div>
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
    // === PORTFOLIO LIGHTBOX WITH ZOOM & PAN ===
    const PORTFOLIO_PHOTOS = @json($portfolioPhotos->map(fn($p) => [
        'src' => str_starts_with($p->filename, 'portfolio/') ? '/storage/'.$p->filename : $p->filename,
    ]));
    let portfolioLbIndex = 0;

    // Zoom/Pan state
    // We use transform-origin: center center, so:
    //   screenPos = viewportCenter + (imagePoint - imageCenter) * zoom + panOffset
    // where panOffset is in screen pixels.
    // Equivalently: screenPos = imagePoint * zoom + (viewportCenter - imageCenter * zoom + panOffset)
    // We store panX, panY as the total translation in screen pixels.
    let zoom = 1;
    let panX = 0;
    let panY = 0;

    let isDragging = false;
    let dragStartClientX = 0;
    let dragStartClientY = 0;
    let dragStartPanX = 0;
    let dragStartPanY = 0;

    let pinchStartDist = 0;
    let pinchStartZoom = 1;

    let zoomIndicatorTimer = null;

    const MIN_ZOOM = 1;
    const MAX_ZOOM = 5;
    const WHEEL_ZOOM_FACTOR = 1.15;
    const BUTTON_ZOOM_FACTOR = 1.4;

    // --- Core transform ---
    function applyTransform() {
        const img = document.getElementById('portfolio-lb-img');
        if (img) {
            img.style.transform = `translate(${panX}px, ${panY}px) scale(${zoom})`;
        }
    }

    function resetZoomPan() {
        zoom = 1;
        panX = 0;
        panY = 0;
        applyTransform();
    }

    // --- Zoom at a screen point (clientX, clientY) ---
    // With transform-origin: center center:
    //   screenPoint = center + (imagePoint - center) * zoom + pan
    // So: imagePoint = center + (screenPoint - center - pan) / zoom
    // After zoom: newPan = screenPoint - center - (imagePoint - center) * newZoom
    //                       = screenPoint - center - (screenPoint - center - pan) / zoom * newZoom
    //                       = (screenPoint - center) * (1 - newZoom/zoom) + pan * (newZoom/zoom)
    function zoomAt(clientX, clientY, newZoom) {
        newZoom = Math.min(MAX_ZOOM, Math.max(MIN_ZOOM, newZoom));
        if (newZoom === zoom) return;

        const img = document.getElementById('portfolio-lb-img');
        const rect = img.getBoundingClientRect();
        const cx = rect.left + rect.width / 2;
        const cy = rect.top + rect.height / 2;

        const scale = newZoom / zoom;
        const dx = clientX - cx;
        const dy = clientY - cy;

        panX = dx * (1 - scale) + panX * scale;
        panY = dy * (1 - scale) + panY * scale;
        zoom = newZoom;

        applyTransform();
        showZoomIndicator();
    }

    function showZoomIndicator() {
        const el = document.getElementById('portfolio-lb-zoom-indicator');
        if (!el) return;
        el.textContent = Math.round(zoom * 100) + '%';
        el.classList.remove('opacity-0');
        clearTimeout(zoomIndicatorTimer);
        zoomIndicatorTimer = setTimeout(() => el.classList.add('opacity-0'), 1200);
    }

    // --- Lightbox open/close ---
    function openPortfolioLightbox(index) {
        portfolioLbIndex = index;
        resetZoomPan();
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
        const img = document.getElementById('portfolio-lb-img');
        img.src = PORTFOLIO_PHOTOS[portfolioLbIndex].src;
        resetZoomPan();
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

    // Browser back
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

    // === DESKTOP: Wheel zoom + drag ===
    const panContainer = document.getElementById('portfolio-lb-pan-container');

    panContainer?.addEventListener('wheel', function(e) {
        e.preventDefault();
        const factor = e.deltaY < 0 ? WHEEL_ZOOM_FACTOR : 1 / WHEEL_ZOOM_FACTOR;
        const newZoom = zoom * factor;
        zoomAt(e.clientX, e.clientY, newZoom);
    }, { passive: false });

    panContainer?.addEventListener('mousedown', function(e) {
        if (e.button !== 0) return;
        isDragging = true;
        dragStartClientX = e.clientX;
        dragStartClientY = e.clientY;
        dragStartPanX = panX;
        dragStartPanY = panY;
        panContainer.style.cursor = 'grabbing';
    });

    document.addEventListener('mousemove', function(e) {
        if (!isDragging) return;
        panX = dragStartPanX + (e.clientX - dragStartClientX);
        panY = dragStartPanY + (e.clientY - dragStartClientY);
        applyTransform();
    });

    document.addEventListener('mouseup', function() {
        if (isDragging) {
            isDragging = false;
            panContainer.style.cursor = zoom > 1 ? 'grab' : 'default';
        }
    });

    // Double-click to toggle zoom
    panContainer?.addEventListener('dblclick', function(e) {
        if (zoom > 1) {
            resetZoomPan();
        } else {
            zoomAt(e.clientX, e.clientY, 2.5);
        }
        showZoomIndicator();
    });

    // === MOBILE: Pinch-to-zoom + single-finger drag + swipe ===
    let touchStartX = 0;
    let touchStartY = 0;
    let touchMoved = false;

    panContainer?.addEventListener('touchstart', function(e) {
        if (e.touches.length === 1) {
            isDragging = true;
            dragStartClientX = e.touches[0].clientX;
            dragStartClientY = e.touches[0].clientY;
            dragStartPanX = panX;
            dragStartPanY = panY;
            touchStartX = e.touches[0].clientX;
            touchStartY = e.touches[0].clientY;
            touchMoved = false;
        } else if (e.touches.length === 2) {
            isDragging = false;
            const t0 = e.touches[0], t1 = e.touches[1];
            pinchStartDist = Math.hypot(t1.clientX - t0.clientX, t1.clientY - t0.clientY);
            pinchStartZoom = zoom;
        }
    }, { passive: true });

    panContainer?.addEventListener('touchmove', function(e) {
        if (e.touches.length === 1 && isDragging) {
            const dx = Math.abs(e.touches[0].clientX - touchStartX);
            const dy = Math.abs(e.touches[0].clientY - touchStartY);
            if (dx > 5 || dy > 5) touchMoved = true;
            // Only pan when zoomed in
            if (zoom > 1) {
                e.preventDefault();
                panX = dragStartPanX + (e.touches[0].clientX - dragStartClientX);
                panY = dragStartPanY + (e.touches[0].clientY - dragStartClientY);
                applyTransform();
            }
        } else if (e.touches.length === 2) {
            e.preventDefault();
            const t0 = e.touches[0], t1 = e.touches[1];
            const dist = Math.hypot(t1.clientX - t0.clientX, t1.clientY - t0.clientY);
            if (pinchStartDist > 0) {
                const newZoom = Math.min(MAX_ZOOM, Math.max(MIN_ZOOM, pinchStartZoom * (dist / pinchStartDist)));
                const cx = (t0.clientX + t1.clientX) / 2;
                const cy = (t0.clientY + t1.clientY) / 2;
                zoomAt(cx, cy, newZoom);
            }
        }
    }, { passive: false });

    panContainer?.addEventListener('touchend', function(e) {
        if (isDragging && e.touches.length === 0) {
            isDragging = false;
            // Swipe to change image only when not zoomed and not dragging
            if (zoom <= 1 && touchMoved && e.changedTouches.length === 1) {
                const dx = e.changedTouches[0].clientX - touchStartX;
                const dy = e.changedTouches[0].clientY - touchStartY;
                if (Math.abs(dx) > Math.abs(dy) && Math.abs(dx) > 50) {
                    if (dx < 0) portfolioLbNext();
                    else portfolioLbPrev();
                }
            }
        }
    }, { passive: true });

    // === KEYBOARD ===
    document.addEventListener('keydown', (e) => {
        const lb = document.getElementById('portfolio-lightbox');
        if (lb.classList.contains('hidden')) return;
        if (e.key === 'Escape') closePortfolioLightbox();
        if (e.key === 'ArrowLeft') portfolioLbPrev();
        if (e.key === 'ArrowRight') portfolioLbNext();
        if (e.key === '+' || e.key === '=') {
            const img = document.getElementById('portfolio-lb-img');
            const rect = img.getBoundingClientRect();
            zoomAt(rect.left + rect.width / 2, rect.top + rect.height / 2, zoom * BUTTON_ZOOM_FACTOR);
        }
        if (e.key === '-') {
            const img = document.getElementById('portfolio-lb-img');
            const rect = img.getBoundingClientRect();
            zoomAt(rect.left + rect.width / 2, rect.top + rect.height / 2, zoom / BUTTON_ZOOM_FACTOR);
        }
        if (e.key === '0') { resetZoomPan(); showZoomIndicator(); }
    });
</script>
@endpush
