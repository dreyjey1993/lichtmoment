@extends('layouts.app')

@section('title', $project->name . ' – Galerie')
@section('og_title', $project->name . ' – Galerie | Lichtmoment')
@section('og_description', 'Fotos & Videos von ' . $project->name . ' – Hochzeitsfotografie von Markus Knuth')

@php $noFooter = false; @endphp

@if($needsPassword)
<style>#gallery-content { display: none; }</style>
@endif

@section('content')
<div class="min-h-screen bg-offwhite flex flex-col">

    {{-- Password Modal --}}
    @if($needsPassword)
    <div id="password-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-offwhite/95 backdrop-blur-sm px-6">
        <div class="w-full max-w-sm text-center">
            <h1 class="font-serif text-3xl text-gold-400 mb-2">Lichtmoment</h1>
            <p class="text-gray-400 text-sm mb-6">Diese Galerie ist passwortgeschützt.</p>
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <form id="password-form">
                    <input type="password" id="share-pass-input" placeholder="Passwort" required
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:border-gold-400 outline-none mb-3">
                    <button type="submit" class="w-full py-3 bg-gold-400 hover:bg-gold-500 text-white font-medium rounded-xl transition-colors">
                        Galerie öffnen
                    </button>
                </form>
                <p id="password-error" class="text-red-500 text-sm mt-3 hidden">Falsches Passwort</p>
            </div>
        </div>
    </div>
    @endif

    <div id="gallery-content" class="flex-1">
        {{-- Header --}}
        <header class="py-8 sm:py-12 px-4 sm:px-6 text-center">
            <h1 class="font-serif text-2xl sm:text-4xl text-gray-700 mb-2">Fotos & Videos</h1>
            <p class="text-gray-400 text-base sm:text-lg">{{ $project->name }}</p>
        </header>

        {{-- Actions --}}
        @if($shareLink->download_enabled)
        <div class="text-center mb-6 sm:mb-8">
            <button onclick="downloadAll()" class="px-5 py-2.5 bg-gold-400 hover:bg-gold-500 text-white font-medium rounded-xl transition-colors text-sm">
                Alle Fotos herunterladen (.zip)
            </button>
        </div>
        @endif

        {{-- Folder breadcrumb --}}
        @if($folders->count() > 1)
        <nav class="max-w-6xl mx-auto px-4 sm:px-6 mb-4 sm:mb-6 flex flex-wrap gap-2">
            <button onclick="filterByFolder(null)" class="text-xs sm:text-sm px-2.5 sm:px-3 py-1 rounded-full border border-gray-200 hover:border-gold-400 transition-colors">Alle Fotos</button>
            @foreach($folders as $folder)
            <button onclick="filterByFolder({{ $folder->id }})" class="text-xs sm:text-sm px-2.5 sm:px-3 py-1 rounded-full border border-gray-200 hover:border-gold-400 transition-colors">{{ $folder->name }}</button>
            @endforeach
        </nav>
        @endif

        {{-- Photo grid --}}
        <div class="max-w-6xl mx-auto px-3 sm:px-6 flex-1">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2 sm:gap-3" id="gallery-grid">
                @foreach($photos as $photo)
                <div class="gallery-item relative aspect-square rounded-xl overflow-hidden bg-gray-100 cursor-pointer group"
                     data-folder="{{ $photo->folder_id ?? 0 }}"
                     onclick="openLightbox({{ $loop->index }})">
                    <img src="/storage/projects/{{ $photo->filename }}" alt="{{ $photo->original_name }}" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                    @if($shareLink->download_enabled)
                    <a href="/share/download/photo/{{ $photo->id }}?token={{ $token }}"
                       class="absolute top-2 right-2 w-8 h-8 bg-white/90 rounded-full flex items-center justify-center text-gray-600 opacity-0 group-hover:opacity-100 transition-opacity"
                       onclick="event.stopPropagation()" title="Herunterladen">⬇</a>
                    @endif
                </div>
                @endforeach
            </div>

            @if($photos->isEmpty())
            <div class="text-center py-20 text-gray-300">Noch keine Fotos hochgeladen.</div>
            @endif
        </div>

        {{-- Footer with photographer --}}
        <footer class="py-8 px-6 text-center border-t border-gray-100 mt-8">
            <p class="text-sm text-gray-400">
                Fotografie von <strong class="text-gray-600">{{ $photographer['name'] }}</strong> –
                <a href="tel:{{ $photographer['phone'] }}" class="text-gold-400 hover:text-gold-500">{{ $photographer['phone'] }}</a> –
                <a href="mailto:{{ $photographer['email'] }}" class="text-gold-400 hover:text-gold-500">{{ $photographer['email'] }}</a>
            </p>
        </footer>
    </div>
</div>

{{-- Lightbox --}}
<div id="lightbox" class="fixed inset-0 z-[9999] bg-black/95 hidden items-center justify-center p-6" aria-hidden="true">
    <button class="absolute top-4 right-4 text-white text-4xl hover:text-gold-400 transition-colors" onclick="closeLightbox()">&times;</button>
    <button class="absolute left-4 top-1/2 -translate-y-1/2 text-white text-3xl hover:text-gold-400 transition-colors hidden md:block" onclick="prevImage()">&#10094;</button>
    <img id="lightbox-img" src="" alt="" class="max-w-[90vw] max-h-[85vh] object-contain rounded-lg">
    <button class="absolute right-4 top-1/2 -translate-y-1/2 text-white text-3xl hover:text-gold-400 transition-colors hidden md:block" onclick="nextImage()">&#10095;</button>
</div>
@endsection

@push('scripts')
<script>
    const TOKEN = '{{ $token }}';
    const PHOTOS = @json($photos->map(fn($p) => ['id' => $p->id, 'filename' => '/storage/projects/' . $p->filename, 'folder_id' => $p->folder_id]));

    // === PASSWORD CHECK ===
    @if($needsPassword)
    document.getElementById('password-form')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const pwd = document.getElementById('share-pass-input').value;
        const res = await fetch('/share/api/check-password', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ token: TOKEN, password: pwd }),
        });
        const data = await res.json();
        if (data.success) {
            document.getElementById('password-modal').style.display = 'none';
            document.getElementById('gallery-content').style.display = 'block';
        } else {
            document.getElementById('password-error').classList.remove('hidden');
        }
    });
    @endif

    // === LIGHTBOX ===
    const lightbox = document.getElementById('lightbox');
    const lightboxImg = document.getElementById('lightbox-img');
    let currentIndex = 0;
    let lbTouchStartX = 0;
    let lbTouchStartY = 0;

    function getVisibleItems() {
        return Array.from(document.querySelectorAll('.gallery-item')).filter(el => el.style.display !== 'none');
    }

    function openLightbox(index) {
        currentIndex = index;
        const items = getVisibleItems();
        if (!items.length) return;
        lightboxImg.src = items[currentIndex].querySelector('img').src;
        lightbox.classList.remove('hidden');
        lightbox.classList.add('flex');
        lightbox.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        document.body.style.position = 'fixed';
        document.body.style.width = '100%';
        // Push TWO history states so browser back/swipe lands on our duplicate
        window.__lbHistoryPushed = true;
        history.pushState({ lightbox: true }, '');
        history.pushState({ lightbox: true }, '');
    }

    function closeLightbox() {
        lightbox.classList.add('hidden');
        lightbox.classList.remove('flex');
        lightbox.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
        document.body.style.position = '';
        document.body.style.width = '';
        if (window.__lbHistoryPushed) {
            window.__lbHistoryPushed = false;
            history.go(-2);
        }
    }

    // Handle browser back / swipe-back while lightbox is open
    window.addEventListener('popstate', function(e) {
        if (lightbox && !lightbox.classList.contains('hidden') && window.__lbHistoryPushed) {
            window.__lbHistoryPushed = false;
            history.pushState({ lightbox: true }, '');
            lightbox.classList.add('hidden');
            lightbox.classList.remove('flex');
            lightbox.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
            document.body.style.position = '';
            document.body.style.width = '';
        }
    });

    function prevImage() {
        const items = getVisibleItems();
        if (!items.length) return;
        currentIndex = (currentIndex - 1 + items.length) % items.length;
        lightboxImg.src = items[currentIndex].querySelector('img').src;
    }

    function nextImage() {
        const items = getVisibleItems();
        if (!items.length) return;
        currentIndex = (currentIndex + 1) % items.length;
        lightboxImg.src = items[currentIndex].querySelector('img').src;
    }

    document.addEventListener('keydown', (e) => {
        if (lightbox.classList.contains('hidden')) return;
        if (e.key === 'Escape') closeLightbox();
        if (e.key === 'ArrowLeft') prevImage();
        if (e.key === 'ArrowRight') nextImage();
    });

    lightbox?.addEventListener('click', (e) => {
        if (e.target === lightbox || e.target.id === 'lightbox-img') closeLightbox();
    });

    // Touch swipe for mobile — block browser back-swipe
    lightbox?.addEventListener('touchstart', (e) => {
        lbTouchStartX = e.touches[0].clientX;
        lbTouchStartY = e.touches[0].clientY;
    }, { passive: true });
    lightbox?.addEventListener('touchmove', (e) => {
        e.preventDefault();
    }, { passive: false });
    lightbox?.addEventListener('touchend', (e) => {
        const dx = e.changedTouches[0].clientX - lbTouchStartX;
        const dy = e.changedTouches[0].clientY - lbTouchStartY;
        if (Math.abs(dx) > Math.abs(dy) && Math.abs(dx) > 50) {
            if (dx < 0) nextImage();
            else prevImage();
        }
    }, { passive: true });

    // === FOLDER FILTER ===
    function filterByFolder(folderId) {
        document.querySelectorAll('.gallery-item').forEach(item => {
            item.style.display = (folderId === null || item.dataset.folder == folderId) ? '' : 'none';
        });
    }

    // === DOWNLOAD ALL ===
    async function downloadAll() {
        const res = await fetch('/share/download/zip', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ token: TOKEN }),
        });
        const blob = await res.blob();
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'fotos.zip';
        a.click();
        URL.revokeObjectURL(url);
    }
</script>
@endpush
