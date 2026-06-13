@extends('layouts.app')

@section('title', 'Neues Projekt – Lichtmoment Admin')

@php $adminNav = true; @endphp

@section('content')
<div class="pt-24 sm:pt-28 pb-12 px-4 sm:px-6">
    <div class="max-w-2xl mx-auto">
        {{-- Header --}}
        <div class="mb-8">
            <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-400 hover:text-gold-400 transition-colors mb-2 inline-block">&larr; Zurück</a>
            <h1 class="font-serif text-2xl sm:text-3xl text-gray-700">Neues Projekt erstellen</h1>
        </div>

        <form action="{{ route('admin.project.create') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="card p-5 sm:p-6 space-y-5">
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-1.5 block">Projektname <span class="text-red-400">*</span></label>
                    <input type="text" name="name" required placeholder="z.B. Sarah & Thomas – Hochzeit im Schloss" class="input">
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-700 mb-1.5 block">Beschreibung</label>
                    <textarea name="description" rows="3" placeholder="Kurze Beschreibung des Projekts..." class="input resize-none"></textarea>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-700 mb-1.5 block">Cover-Bild <span class="text-xs text-gray-400 font-normal">(optional)</span></label>
                    <div class="relative">
                        <input type="file" name="cover_image" accept="image/jpeg,image/png,image/webp" id="cover-input"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                        <div class="border-2 border-dashed border-gray-200 rounded-2xl p-6 text-center hover:border-gold-300 hover:bg-gold-50/30 transition-all duration-200" id="cover-dropzone">
                            <svg class="w-8 h-8 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="text-sm text-gray-500">Klicken oder Bild hierher ziehen</p>
                            <p class="text-xs text-gray-400 mt-1">JPG, PNG, WebP</p>
                        </div>
                    </div>
                    <div id="cover-preview" class="hidden mt-3">
                        <img src="" alt="Vorschau" class="w-full max-h-48 object-cover rounded-xl border border-gray-200">
                        <button type="button" onclick="removeCover()" class="btn btn-danger btn-sm mt-2">Entfernen</button>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-ghost btn-md">Abbrechen</a>
                <button type="submit" class="btn btn-primary btn-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Projekt erstellen
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const coverInput = document.getElementById('cover-input');
    const coverDropzone = document.getElementById('cover-dropzone');
    const coverPreview = document.getElementById('cover-preview');
    const coverImg = coverPreview.querySelector('img');

    coverInput?.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                coverImg.src = e.target.result;
                coverPreview.classList.remove('hidden');
                coverDropzone.classList.add('hidden');
            };
            reader.readAsDataURL(this.files[0]);
        }
    });

    function removeCover() {
        coverInput.value = '';
        coverPreview.classList.add('hidden');
        coverDropzone.classList.remove('hidden');
    }
</script>
@endpush
