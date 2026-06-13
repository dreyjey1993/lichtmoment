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

<!-- Portfolio -->
<section class="py-16 sm:py-24 px-4 sm:px-6 max-w-6xl mx-auto" id="portfolio">
    <h2 class="font-serif text-3xl sm:text-4xl font-light text-center mb-8 sm:mb-12 text-gray-700">Portfolio</h2>
    <div class="columns-1 md:columns-2 lg:columns-3 gap-4 sm:gap-6">
        @forelse($portfolioPhotos as $photo)
        <div class="break-inside-avoid mb-4 sm:mb-6 rounded-xl overflow-hidden shadow-sm bg-white hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
            @if(str_starts_with($photo->filename, 'portfolio/'))
                <img src="/storage/{{ $photo->filename }}" alt="{{ $photo->original_name }}" class="w-full" loading="lazy">
            @else
                <img src="{{ $photo->filename }}" alt="{{ $photo->original_name }}" class="w-full" loading="lazy">
            @endif
        </div>
        @empty
            @for($i = 1; $i <= 6; $i++)
            <div class="break-inside-avoid mb-4 sm:mb-6 rounded-xl overflow-hidden shadow-sm bg-white">
                <div class="w-full aspect-[4/3] bg-gradient-to-br from-gold-50 to-gold-100 flex items-center justify-center">
                    <span class="font-serif text-2xl sm:text-3xl text-gold-300">{{ $i }}</span>
                </div>
            </div>
            @endfor
        @endforelse
    </div>
</section>

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
