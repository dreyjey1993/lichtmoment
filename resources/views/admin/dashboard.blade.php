@extends('layouts.app')

@section('title', 'Dashboard – Lichtmoment Admin')

@php $adminNav = true; @endphp

@section('content')
<div class="pt-24 sm:pt-28 pb-8 sm:pb-12 px-4 sm:px-6">
    <div class="max-w-6xl mx-auto">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-10">
            <h1 class="font-serif text-2xl sm:text-3xl text-gray-700">Dashboard</h1>
            <form action="{{ route('admin.project.create') }}" method="POST" class="flex gap-2 items-center">
                @csrf
                <input type="text" name="name" placeholder="Projektname..." required
                       class="px-3 py-2 border border-gray-200 rounded-lg text-sm focus:border-gold-400 outline-none w-40 sm:w-56">
                <button type="submit" class="px-4 py-2.5 bg-gold-400 hover:bg-gold-500 text-white text-sm font-medium rounded-xl transition-colors shrink-0">
                    + Neu
                </button>
            </form>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10">
            <div class="bg-white rounded-xl border border-gray-100 p-6 text-center">
                <p class="text-3xl font-light text-gold-400">{{ $projects->count() }}</p>
                <p class="text-xs text-gray-400 uppercase tracking-wider mt-1">Projekte</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 p-6 text-center">
                <p class="text-3xl font-light text-gold-400">{{ $projects->sum('photos_count') }}</p>
                <p class="text-xs text-gray-400 uppercase tracking-wider mt-1">Fotos</p>
            </div>
        </div>

        {{-- Projects --}}
        @if($projects->isEmpty())
        <div class="text-center py-20">
            <p class="text-gray-400 mb-4">Noch keine Projekte erstellt.</p>
            <a href="{{ route('admin.project.new') }}" class="px-5 py-2.5 bg-gold-400 hover:bg-gold-500 text-white text-sm font-medium rounded-xl transition-colors">
                Erstes Projekt erstellen
            </a>
        </div>
        @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($projects as $project)
            <div class="group bg-white rounded-xl border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-300 relative">
                <a href="{{ route('admin.project.detail', $project->id) }}">
                    <div class="aspect-[16/10] bg-gradient-to-br from-gold-50 to-gold-100 flex items-center justify-center">
                        @if($project->cover_image)
                            <img src="/storage/projects/{{ $project->cover_image }}" alt="{{ $project->name }}" class="w-full h-full object-cover">
                        @else
                            <span class="font-serif text-4xl text-gold-300">{{ substr($project->name, 0, 1) }}</span>
                        @endif
                    </div>
                    <div class="p-5">
                        <h3 class="font-medium text-gray-700 group-hover:text-gold-500 transition-colors">{{ $project->name }}</h3>
                        <div class="flex items-center gap-3 mt-2 text-xs text-gray-400">
                            <span>{{ $project->photos_count }} Fotos</span>
                            <span>{{ $project->created_at->format('d.m.Y') }}</span>
                        </div>
                    </div>
                </a>
                <button onclick="deleteProject({{ $project->id }}, '{{ addslashes($project->name) }}')" class="absolute top-3 right-3 w-8 h-8 bg-red-500/80 hover:bg-red-600 text-white rounded-lg flex items-center justify-center transition-all duration-200 cursor-pointer" title="Projekt löschen">
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
