@extends('layouts.app')

@section('title', 'Dashboard – Lichtmoment Admin')

@php $adminNav = true; @endphp

@section('content')
<div class="pt-24 sm:pt-28 pb-8 sm:pb-12 px-4 sm:px-6">
    <div class="max-w-6xl mx-auto">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <h1 class="font-serif text-2xl sm:text-3xl text-gray-700">Dashboard</h1>
            <a href="{{ route('admin.project.new') }}" class="btn btn-primary btn-lg shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Neues Projekt
            </a>
        </div>

        {{-- Quick Create (collapsed by default on mobile) --}}
        <details class="sm:hidden mb-6 card overflow-hidden">
            <summary class="px-4 py-3 text-sm font-medium text-gray-600 cursor-pointer hover:bg-gray-50">Schnell erstellen</summary>
            <div class="px-4 pb-4 border-t border-gray-100 pt-3">
                <form action="{{ route('admin.project.create') }}" method="POST" class="flex gap-2">
                    @csrf
                    <input type="text" name="name" placeholder="Projektname..." required class="input flex-1 min-w-0">
                    <button type="submit" class="btn btn-primary btn-md shrink-0">Erstellen</button>
                </form>
            </div>
        </details>

        {{-- Stats --}}
        <div class="grid grid-cols-2 gap-3 sm:gap-4 mb-8">
            <div class="card p-4 sm:p-6 text-center">
                <p class="text-2xl sm:text-3xl font-light text-gold-400">{{ $projects->count() }}</p>
                <p class="text-xs text-gray-400 uppercase tracking-wider mt-1">Projekte</p>
            </div>
            <div class="card p-4 sm:p-6 text-center">
                <p class="text-2xl sm:text-3xl font-light text-gold-400">{{ $projects->sum('photos_count') }}</p>
                <p class="text-xs text-gray-400 uppercase tracking-wider mt-1">Fotos</p>
            </div>
        </div>

        {{-- Projects --}}
        @if($projects->isEmpty())
        <div class="card text-center py-20">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gold-50 flex items-center justify-center">
                <svg class="w-8 h-8 text-gold-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
            </div>
            <p class="text-gray-400 mb-4">Noch keine Projekte erstellt.</p>
            <a href="{{ route('admin.project.new') }}" class="btn btn-primary btn-lg">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Erstes Projekt erstellen
            </a>
        </div>
        @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
            @foreach($projects as $project)
            <div class="card card-hover group relative">
                <a href="{{ route('admin.project.detail', $project->id) }}" class="block">
                    <div class="aspect-[16/10] bg-gradient-to-br from-gold-50 to-gold-100 flex items-center justify-center overflow-hidden">
                        @if($project->cover_image)
                            <img src="/storage/projects/{{ $project->cover_image }}" alt="{{ $project->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <span class="font-serif text-5xl text-gold-200">{{ substr($project->name, 0, 1) }}</span>
                        @endif
                    </div>
                    <div class="p-4 sm:p-5">
                        <h3 class="font-medium text-gray-700 group-hover:text-gold-500 transition-colors truncate">{{ $project->name }}</h3>
                        @if($project->description)
                        <p class="text-xs text-gray-400 mt-1 line-clamp-2">{{ $project->description }}</p>
                        @endif
                        <div class="flex items-center gap-3 mt-3 text-xs text-gray-400">
                            <span class="inline-flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                {{ $project->photos_count }}
                            </span>
                            <span>{{ $project->created_at->format('d.m.Y') }}</span>
                        </div>
                    </div>
                </a>
                <button onclick="deleteProject({{ $project->id }}, '{{ addslashes($project->name) }}')" class="btn-icon absolute top-3 right-3 bg-red-500/90 hover:bg-red-600 text-white shadow-sm" title="Projekt löschen">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function deleteProject(id, name) {
    if (!confirm('Projekt "' + name + '" wirklich löschen?')) return;
    fetch('/admin/api/delete?type=project&id=' + id)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showToast('Projekt gelöscht');
                setTimeout(() => location.reload(), 500);
            } else {
                showToast('Fehler beim Löschen', 'error');
            }
        })
        .catch(() => showToast('Fehler beim Löschen', 'error'));
}
</script>
@endpush
@endsection
