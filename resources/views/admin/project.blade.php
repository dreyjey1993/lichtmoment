@extends('layouts.app')

@section('title', $project->name . ' – Lichtmoment Admin')

@php $adminNav = true; @endphp

@section('content')
<div class="pt-24 sm:pt-28 pb-8 sm:pb-12 px-4 sm:px-6">
    <div class="max-w-6xl mx-auto">

        {{-- Header --}}
        <div class="mb-8 flex flex-col sm:flex-row sm:items-start justify-between gap-4">
            <div>
                <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-400 hover:text-gold-400 transition-colors mb-2 inline-block">&larr; Zurück</a>
                <h1 class="font-serif text-2xl sm:text-3xl text-gray-700">{{ $project->name }}</h1>
                @if($project->description)<p class="text-gray-400 mt-1 text-sm">{{ $project->description }}</p>@endif
            </div>
            <button onclick="deleteProject({{ $project->id }}, '{{ addslashes($project->name) }}')" class="btn btn-danger btn-md shrink-0 self-start">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Projekt löschen
            </button>
        </div>

        {{-- Settings Row --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
            <div class="card p-5 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium text-gray-700">Download erlaubt</h3>
                    <p class="text-xs text-gray-400">Brautpaare können Fotos herunterladen</p>
                </div>
                <label class="toggle">
                    <input type="checkbox" id="toggle-download" {{ $project->download_enabled ? 'checked' : '' }}>
                    <div class="toggle-track"></div>
                    <div class="toggle-thumb"></div>
                </label>
            </div>
            <div class="card p-5">
                <h3 class="text-sm font-medium text-gray-700 mb-2">Projekt-Passwort</h3>
                <div class="flex gap-2">
                    <input type="text" id="project-password" placeholder="Kein Passwort" class="input flex-1">
                    <button onclick="updatePassword()" class="btn btn-primary btn-md">Setzen</button>
                </div>
            </div>
        </div>

        {{-- Two Column Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left: Folders + Photos --}}
            <div class="lg:col-span-2 card p-4 sm:p-6">

                {{-- Header with bulk actions --}}
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-medium text-gray-700">Ordner & Fotos</h2>
                    @if($photos->count() > 0)
                    <div class="flex items-center gap-2">
                        <button onclick="toggleSelectMode()" id="select-mode-btn" class="btn btn-ghost btn-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Auswahl
                        </button>
                        <button onclick="deleteAllPhotos()" class="btn btn-danger btn-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Alle löschen
                        </button>
                    </div>
                    @endif
                </div>

                {{-- New Folder --}}
                <div class="flex flex-wrap gap-2 mb-5">
                    <input type="text" id="new-folder-name" placeholder="Neuer Ordner..." class="input flex-1 min-w-[120px]">
                    <select id="new-folder-parent" class="input flex-1 min-w-[100px]">
                        <option value="">Ohne Elternordner</option>
                        @foreach($folders as $folder)
                        <option value="{{ $folder->id }}">{{ $folder->name }}</option>
                        @endforeach
                    </select>
                    <button onclick="createFolder()" class="btn btn-primary btn-md shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </button>
                </div>

                {{-- Folder Tabs --}}
                @if($folders->count() > 0)
                <div class="flex flex-wrap gap-2 mb-4">
                    <button onclick="filterPhotosByFolder(null)" class="tab {{ !request('folder') ? 'tab-active' : 'tab-inactive' }}" data-folder="">
                        Alle Fotos
                        <span class="badge badge-gray ml-1">{{ $photos->count() }}</span>
                    </button>
                    @foreach($folders as $folder)
                    <div class="inline-flex items-center">
                        <button onclick="filterPhotosByFolder({{ $folder->id }})" class="tab {{ request('folder') == $folder->id ? 'tab-active' : 'tab-inactive' }} !rounded-r-none !border-r-0" data-folder="{{ $folder->id }}">
                            {{ $folder->name }}
                            <span class="badge badge-gray ml-1">{{ $photos->where('folder_id', $folder->id)->count() }}</span>
                        </button>
                        <button onclick="deleteFolder({{ $folder->id }}, '{{ addslashes($folder->name) }}')" class="btn-icon-sm !rounded-l-none border border-l-0 border-gray-200 text-red-400 hover:text-white hover:bg-red-500 hover:border-red-500" title="Ordner löschen">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    @endforeach
                </div>
                @endif

                {{-- Bulk Delete Bar --}}
                <div id="bulk-bar" class="hidden mb-4 p-4 bg-red-50 border border-red-200 rounded-xl">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-red-600 font-medium"><span id="selected-count">0</span> ausgewählt</span>
                        <div class="flex items-center gap-2">
                            <button onclick="clearSelection()" class="btn btn-ghost btn-sm">Aufheben</button>
                            <button onclick="bulkDeletePhotos()" class="btn btn-danger-solid btn-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                Löschen
                            </button>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 mt-3 pt-3 border-t border-red-200">
                        <button onclick="selectAllPhotos()" class="text-xs text-red-500 hover:text-red-700 font-medium">Alle auswählen</button>
                        <span class="text-red-300">|</span>
                        <button onclick="clearSelection()" class="text-xs text-red-500 hover:text-red-700 font-medium">Auswahl aufheben</button>
                    </div>
                </div>

                {{-- Upload Dropzone --}}
                <div id="dropzone" class="border-2 border-dashed border-gray-200 rounded-2xl p-6 sm:p-8 text-center mb-6 cursor-pointer hover:border-gold-300 hover:bg-gold-50/30 transition-all duration-200">
                    <svg class="w-10 h-10 mx-auto text-gold-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M17 8l-5-5-5 5M12 3v12"/>
                    </svg>
                    <p class="text-sm text-gray-500">Fotos hierher ziehen oder klicken</p>
                    <p class="text-xs text-gray-300 mt-1">JPG, PNG, WebP – max. 20MB</p>
                    <input type="file" id="file-input" multiple accept="image/jpeg,image/png,image/webp" class="hidden">
                </div>

                {{-- Photo Grid --}}
                <div class="grid grid-cols-2 md:grid-cols-3 gap-2 sm:gap-3" id="photo-grid">
                    @foreach($photos as $photo)
                    <div class="relative aspect-square rounded-xl overflow-hidden bg-gray-100 group photo-item cursor-pointer" data-id="{{ $photo->id }}" data-folder="{{ $photo->folder_id ?? 0 }}" data-photo-id="{{ $photo->id }}">
                        {{-- Checkbox (shown in select mode) --}}
                        <div class="photo-checkbox-wrapper absolute top-2 left-2 z-10 hidden">
                            <input type="checkbox" class="checkbox photo-checkbox" value="{{ $photo->id }}" onchange="updateBulkBar()">
                        </div>
                        {{-- Hover overlay --}}
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors pointer-events-none z-[1]"></div>
                        <img src="/storage/projects/{{ $photo->filename }}" alt="{{ $photo->original_name }}" class="w-full h-full object-cover" draggable="false">
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-2 flex items-end justify-between z-[2]">
                            <span class="text-white text-xs truncate drop-shadow-sm">{{ $photo->original_name }}</span>
                            <button onclick="event.stopPropagation(); deletePhoto({{ $photo->id }})" class="btn-icon-sm text-white/70 hover:text-white hover:bg-red-500/80 ml-2 shrink-0" title="Foto löschen">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>

                @if($photos->isEmpty())
                <div class="text-center py-12">
                    <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-gray-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <p class="text-gray-400 text-sm">Noch keine Fotos hochgeladen.</p>
                </div>
                @endif
            </div>

            {{-- Right: Share Links --}}
            <div class="card p-4 sm:p-6">
                <h2 class="font-medium text-gray-700 mb-4">Share-Links</h2>

                {{-- Create Share --}}
                <div class="space-y-3 mb-6">
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="text-xs text-gray-400 mb-1 block">Ablauf (Tage)</label>
                            <input type="number" id="share-expiry" placeholder="Unbegrenzt" min="1" class="input input-sm">
                        </div>
                        <div>
                            <label class="text-xs text-gray-400 mb-1 block">Passwort</label>
                            <input type="text" id="share-password" placeholder="Optional" class="input input-sm">
                        </div>
                    </div>
                    <label class="toggle">
                        <input type="checkbox" id="share-download" checked>
                        <div class="toggle-track"></div>
                        <div class="toggle-thumb"></div>
                    </label>
                    <span class="text-sm text-gray-600">Download erlaubt</span>
                    <button onclick="createShare()" class="btn btn-primary btn-md w-full">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                        Share-Link erstellen
                    </button>
                </div>

                {{-- Existing Shares --}}
                <div class="space-y-3" id="share-list">
                    @forelse($shares as $share)
                    <div class="share-card" data-id="{{ $share->id }}">
                        <code class="text-xs text-gold-500 block truncate font-mono">{{ $share->token }}</code>
                        <div class="flex items-center gap-2 mt-1.5 text-xs text-gray-400">
                            <span class="inline-flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                {{ $share->access_count }}
                            </span>
                            @if($share->expires_at)
                            <span class="inline-flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ $share->expires_at->format('d.m.Y') }}
                            </span>
                            @endif
                            @if($share->password_hash)<span>🔒</span>@endif
                        </div>
                        <div class="flex gap-2 mt-3">
                            <button onclick="copyShare('{{ $share->token }}')" class="btn btn-secondary btn-sm flex-1">Kopieren</button>
                            <button onclick="deleteShare({{ $share->id }})" class="btn btn-danger btn-sm flex-1">Löschen</button>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-6">
                        <svg class="w-8 h-8 mx-auto text-gray-200 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                        <p class="text-xs text-gray-400">Noch keine Share-Links erstellt.</p>
                    </div>
                    @endforelse
                </div>
            </div>
    </div>
</div>

{{-- Admin Lightbox --}}
<div id="admin-lightbox" class="fixed inset-0 z-[9999] bg-black/95 hidden items-center justify-center" aria-hidden="true" onclick="if(event.target===this) closeAdminLightbox()" style="touch-action: none;">
    {{-- Full-width touch zones for prev/next on mobile --}}
    <div class="absolute inset-y-0 left-0 w-1/4 sm:hidden z-10" onclick="adminLbPrev()"></div>
    <div class="absolute inset-y-0 right-0 w-1/4 sm:hidden z-10" onclick="adminLbNext()"></div>
    {{-- Close button --}}
    <button class="absolute top-3 right-3 sm:top-4 sm:right-4 w-10 h-10 flex items-center justify-center text-white/70 hover:text-white hover:bg-white/10 rounded-xl transition-colors z-20" onclick="closeAdminLightbox()">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
    {{-- Counter --}}
    <div class="absolute top-3 left-3 sm:top-4 sm:left-4 text-white/50 text-xs font-mono z-20" id="lb-counter"></div>
    {{-- Prev button (desktop + mobile: directly at edge) --}}
    <button class="absolute left-1 sm:left-3 top-1/2 -translate-y-1/2 w-12 h-12 sm:w-14 sm:h-14 flex items-center justify-center text-white/50 hover:text-white hover:bg-white/10 rounded-xl transition-colors z-20" onclick="adminLbPrev()">
        <svg class="w-7 h-7 sm:w-9 sm:h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </button>
    {{-- Image --}}
    <img id="admin-lightbox-img" src="" alt="" class="max-w-[90vw] max-h-[85vh] sm:max-w-[85vw] sm:max-h-[80vh] object-contain rounded-lg select-none" draggable="false">
    {{-- Next button (desktop + mobile: directly at edge) --}}
    <button class="absolute right-1 sm:right-3 top-1/2 -translate-y-1/2 w-12 h-12 sm:w-14 sm:h-14 flex items-center justify-center text-white/50 hover:text-white hover:bg-white/10 rounded-xl transition-colors z-20" onclick="adminLbNext()">
        <svg class="w-7 h-7 sm:w-9 sm:h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    </button>
</div>

@push('scripts')
<script>
try {
    const PROJECT_ID = {{ $project->id }};
    let selectMode = false;

    // === SELECT MODE ===
    function toggleSelectMode() {
        selectMode = !selectMode;
        const btn = document.getElementById('select-mode-btn');
        const checkboxes = document.querySelectorAll('.photo-checkbox-wrapper');

        if (selectMode) {
            btn.classList.add('!bg-gold-50', '!border-gold-300', '!text-gold-600');
            btn.classList.remove('text-gray-500');
            checkboxes.forEach(cb => cb.classList.remove('hidden'));
        } else {
            btn.classList.remove('!bg-gold-50', '!border-gold-300', '!text-gold-600');
            btn.classList.add('text-gray-500');
            checkboxes.forEach(cb => cb.classList.add('hidden'));
            clearSelection();
        }
    }

    function handlePhotoClick(event, photoId) {
        if (selectMode) {
            const checkbox = event.target.closest('.photo-item').querySelector('.photo-checkbox');
            checkbox.checked = !checkbox.checked;
            updateBulkBar();
        } else {
            openAdminLightbox(photoId);
        }
    }

    // Grid click delegation
    document.getElementById('photo-grid')?.addEventListener('click', function(e) {
        const item = e.target.closest('.photo-item');
        if (!item) return;
        // Don't trigger on checkbox or delete button
        if (e.target.closest('.photo-checkbox-wrapper') || e.target.closest('[onclick*="deletePhoto"]')) return;
        const photoId = item.dataset.photoId;
        if (photoId) handlePhotoClick(e, photoId);
    });

    // === UPLOAD ===
    const dropzone = document.getElementById('dropzone');
    const fileInput = document.getElementById('file-input');

    if (dropzone) {
        dropzone.addEventListener('click', () => fileInput.click());
        dropzone.addEventListener('dragover', (e) => { e.preventDefault(); dropzone.classList.add('border-gold-400', 'bg-gold-50/30'); });
        dropzone.addEventListener('dragleave', () => { dropzone.classList.remove('border-gold-400', 'bg-gold-50/30'); });
        dropzone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropzone.classList.remove('border-gold-400', 'bg-gold-50/30');
            uploadFiles(e.dataTransfer.files);
        });
    }

    fileInput?.addEventListener('change', () => uploadFiles(fileInput.files));

    async function uploadFiles(files) {
        const fd = new FormData();
        fd.append('project_id', PROJECT_ID);
        for (const f of files) fd.append('photos[]', f);

        try {
            const res = await fetch('/admin/upload', { method: 'POST', body: fd });
            const data = await res.json();
            if (data.success) {
                showToast(`${data.uploaded.length} Fotos hochgeladen`);
                location.reload();
            } else {
                showToast(data.error || 'Upload fehlgeschlagen', 'error');
            }
        } catch (e) {
            showToast('Verbindungsfehler', 'error');
        }
    }

    // === FOLDERS ===
    async function createFolder() {
        const name = document.getElementById('new-folder-name').value.trim();
        const parentId = document.getElementById('new-folder-parent').value || null;
        if (!name) return showToast('Ordnername fehlt', 'error');

        const fd = new FormData();
        fd.append('project_id', PROJECT_ID);
        fd.append('name', name);
        if (parentId) fd.append('parent_id', parentId);

        const res = await fetch('/admin/folder/create', { method: 'POST', body: fd });
        const data = await res.json();
        if (data.success) {
            showToast('Ordner erstellt');
            location.reload();
        }
    }

    function filterPhotosByFolder(folderId) {
        document.querySelectorAll('.photo-item').forEach(item => {
            item.style.display = (folderId === null || item.dataset.folder == folderId) ? '' : 'none';
        });
        document.querySelectorAll('.folder-tab').forEach(tab => {
            const isActive = folderId === null ? tab.dataset.folder === '' : tab.dataset.folder == folderId;
            tab.classList.toggle('tab-active', isActive);
            tab.classList.toggle('tab-inactive', !isActive);
        });
    }

    async function deleteFolder(id, name) {
        if (!confirm('Ordner "' + name + '" und alle enthaltenen Fotos löschen?')) return;
        const res = await fetch('/admin/api/delete?type=folder&id=' + id);
        const data = await res.json();
        if (data.success) {
            showToast('Ordner gelöscht');
            location.reload();
        } else {
            showToast('Fehler beim Löschen', 'error');
        }
    }

    // === SETTINGS ===
    document.getElementById('toggle-download')?.addEventListener('change', function() {
        const body = new URLSearchParams();
        body.append('download_enabled', this.checked ? 1 : 0);
        fetch(`/admin/project/${PROJECT_ID}/settings`, { method: 'POST', body: body });
        showToast('Einstellung aktualisiert');
    });

    async function updatePassword() {
        const pwd = document.getElementById('project-password').value;
        if (!pwd) return showToast('Passwort eingeben', 'error');
        const body = new URLSearchParams();
        body.append('password', pwd);
        await fetch(`/admin/project/${PROJECT_ID}/settings`, { method: 'POST', body: body });
        showToast('Passwort gesetzt');
    }

    // === SHARE LINKS ===
    async function createShare() {
        const fd = new FormData();
        fd.append('project_id', PROJECT_ID);
        const expiry = document.getElementById('share-expiry').value;
        if (expiry) fd.append('expires_days', expiry);
        const password = document.getElementById('share-password').value;
        if (password) fd.append('password', password);
        fd.append('download_enabled', document.getElementById('share-download').checked ? '1' : '0');

        const res = await fetch('/admin/share/create', { method: 'POST', body: fd });
        const data = await res.json();
        if (data.success) {
            showToast('Share-Link erstellt');
            location.reload();
        }
    }

    function copyShare(token) {
        const url = location.origin + '/share/' + token;
        navigator.clipboard.writeText(url).then(() => showToast('Link kopiert'));
    }

    // === ADMIN LIGHTBOX ===
    const ADMIN_PHOTOS = @json($photos->map(fn($p) => ['id' => $p->id, 'filename' => '/storage/projects/' . $p->filename]));
    let adminLightboxIndex = 0;
    let lbTouchStartX = 0;
    let lbTouchStartY = 0;

    function updateLightbox() {
        document.getElementById('admin-lightbox-img').src = ADMIN_PHOTOS[adminLightboxIndex].filename;
        const counter = document.getElementById('lb-counter');
        if (counter) counter.textContent = (adminLightboxIndex + 1) + ' / ' + ADMIN_PHOTOS.length;
    }

    function openAdminLightbox(photoId) {
        const idx = ADMIN_PHOTOS.findIndex(p => p.id == photoId);
        if (idx === -1) return;
        adminLightboxIndex = idx;
        updateLightbox();
        const lb = document.getElementById('admin-lightbox');
        lb.classList.remove('hidden');
        lb.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeAdminLightbox() {
        const lb = document.getElementById('admin-lightbox');
        lb.classList.add('hidden');
        lb.classList.remove('flex');
        document.body.style.overflow = '';
    }

    function adminLbPrev() {
        adminLightboxIndex = (adminLightboxIndex - 1 + ADMIN_PHOTOS.length) % ADMIN_PHOTOS.length;
        updateLightbox();
    }

    function adminLbNext() {
        adminLightboxIndex = (adminLightboxIndex + 1) % ADMIN_PHOTOS.length;
        updateLightbox();
    }

    // Touch swipe for mobile — block browser back-swipe
    const lbEl = document.getElementById('admin-lightbox');
    lbEl?.addEventListener('touchstart', (e) => {
        lbTouchStartX = e.touches[0].clientX;
        lbTouchStartY = e.touches[0].clientY;
    }, { passive: true });
    lbEl?.addEventListener('touchmove', (e) => {
        // Prevent browser back-swipe gesture when lightbox is open
        e.preventDefault();
    }, { passive: false });
    lbEl?.addEventListener('touchend', (e) => {
        const dx = e.changedTouches[0].clientX - lbTouchStartX;
        const dy = e.changedTouches[0].clientY - lbTouchStartY;
        if (Math.abs(dx) > Math.abs(dy) && Math.abs(dx) > 50) {
            if (dx < 0) adminLbNext();
            else adminLbPrev();
        }
    }, { passive: true });

    // === BULK DELETE ===
    function updateBulkBar() {
        const checked = document.querySelectorAll('.photo-checkbox:checked');
        const bar = document.getElementById('bulk-bar');
        const count = document.getElementById('selected-count');
        if (checked.length > 0) {
            bar.classList.remove('hidden');
            count.textContent = checked.length;
        } else {
            bar.classList.add('hidden');
        }
    }

    function selectAllPhotos() {
        document.querySelectorAll('.photo-checkbox').forEach(cb => cb.checked = true);
        updateBulkBar();
    }

    function clearSelection() {
        document.querySelectorAll('.photo-checkbox').forEach(cb => cb.checked = false);
        updateBulkBar();
    }

    async function bulkDeletePhotos() {
        const ids = Array.from(document.querySelectorAll('.photo-checkbox:checked')).map(cb => parseInt(cb.value));
        if (!ids.length) return;
        if (!confirm(ids.length + ' Fotos wirklich löschen?')) return;

        const res = await fetch('/admin/api/bulk-delete-photos', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''},
            body: JSON.stringify({photo_ids: ids}),
        });
        const data = await res.json();
        if (data.success) {
            showToast(data.deleted + ' Fotos gelöscht');
            location.reload();
        } else {
            showToast('Fehler beim Löschen', 'error');
        }
    }

    async function deleteAllPhotos() {
        if (!confirm('ALLE Fotos in diesem Projekt wirklich löschen?')) return;
        const res = await fetch('/admin/api/delete-all-photos', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''},
            body: JSON.stringify({project_id: PROJECT_ID}),
        });
        const data = await res.json();
        if (data.success) {
            showToast(data.deleted + ' Fotos gelöscht');
            location.reload();
        } else {
            showToast('Fehler beim Löschen', 'error');
        }
    }

    // === DELETE ===
    function deleteProject(id, name) {
        if (!confirm('Projekt "' + name + '" wirklich löschen?')) return;
        fetch('/admin/api/delete?type=project&id=' + id)
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showToast('Projekt gelöscht');
                    setTimeout(() => location.href = '/admin', 500);
                } else {
                    showToast('Fehler beim Löschen', 'error');
                }
            })
            .catch(() => showToast('Fehler beim Löschen', 'error'));
    }

    async function deletePhoto(id) {
        if (!confirm('Foto löschen?')) return;
        await fetch(`/admin/api/delete?type=photo&id=${id}`);
        document.querySelector(`[data-id="${id}"]`)?.remove();
        showToast('Foto gelöscht');
    }

    async function deleteShare(id) {
        if (!confirm('Share-Link löschen?')) return;
        await fetch(`/admin/api/delete?type=share&id=${id}`);
        document.querySelector(`[data-id="${id}"]`)?.remove();
        showToast('Share-Link gelöscht');
    }

    // === KEYBOARD ===
    document.addEventListener('keydown', (e) => {
        const lb = document.getElementById('admin-lightbox');
        if (!lb.classList.contains('hidden')) {
            if (e.key === 'Escape') closeAdminLightbox();
            if (e.key === 'ArrowLeft') adminLbPrev();
            if (e.key === 'ArrowRight') adminLbNext();
        }
    });
} catch(e) { console.error('Admin script error:', e.message, e.stack); }
</script>
@endpush
@endsection
