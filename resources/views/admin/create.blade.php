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

            <div class="bg-white rounded-xl border border-gray-100 p-6 space-y-5">
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-1 block">Projektname <span class="text-red-400">*</span></label>
                    <input type="text" name="name" required placeholder="z.B. Sarah & Thomas – Hochzeit im Schloss"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:border-gold-400 outline-none">
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-700 mb-1 block">Beschreibung</label>
                    <textarea name="description" rows="3" placeholder="Kurze Beschreibung des Projekts..."
                              class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:border-gold-400 outline-none resize-none"></textarea>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-700 mb-1 block">Cover-Bild (optional)</label>
                    <input type="file" name="cover_image" accept="image/jpeg,image/png,image/webp"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:bg-gold-50 file:text-gold-600 file:text-xs file:font-medium hover:file:bg-gold-100">
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('admin.dashboard') }}" class="px-5 py-2.5 text-sm text-gray-500 hover:text-gray-700 transition-colors">Abbrechen</a>
                <button type="submit" class="px-6 py-2.5 bg-gold-400 hover:bg-gold-500 text-white text-sm font-medium rounded-xl transition-colors">
                    Projekt erstellen
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
