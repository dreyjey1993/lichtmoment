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
            <button onclick="deleteProject({{ $project->id }}, '{{ addslashes($project->name) }}')" class="px-4 py-2 text-sm text-red-500 border border-red-200 hover:bg-red-50 rounded-lg transition-colors shrink-0 self-start">
                Projekt löschen
            </button>
        </div>

        {{-- Settings Row --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
            <div class="bg-white rounded-xl border border-gray-100 p-5 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium text-gray-700">Download erlaubt</h3>
                    <p class="text-xs text-gray-400">Brautpaare können Fotos herunterladen</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" id="toggle-download" class="sr-only peer" {{ $project->download_enabled ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gold-400"></div>
                </label>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 p-5">
                <h3 class="text-sm font-medium text-gray-700 mb-2">Projekt-Passwort</h3>
                <div class="flex gap-2">
                    <input type="text" id="project-password" placeholder="Kein Passwort" class="flex-1 px-3 py-2 border border-gray-200 rounded-lg text-sm focus:border-gold-400 outline-none">
                    <button onclick="updatePassword()" class="px-4 py-2 bg-gold-400 hover:bg-gold-500 text-white text-sm rounded-lg transition-colors">Setzen</button>
                </div>
            </div>
        </div>

        {{-- Two Column Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left: Folders + Photos --}}
            <div class="lg:col-span-2 bg-white rounded-xl border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-medium text-gray-700">Ordner & Fotos</h2>
                    <div class="flex items-center gap-2">
                        @if($photos->count() > 0)
                        <button onclick="selectAllPhotos()" class="text-xs text-gray-400 hover:text-gray-600 transition-colors">Alle auswählen</button>
                        <button onclick="deleteAllPhotos()" class="text-xs text-red-400 hover:text-red-600 transition-colors">Alle löschen</button>
                        @endif
                    </div>
                </div>

                {{-- New Folder --}}
                <div class="flex flex-wrap gap-2 mb-6">
                    <input type="text" id="new-folder-name" placeholder="Neuer Ordner..." class="flex-1 min-w-[120px] px-3 py-2 border border-gray-200 rounded-lg text-sm focus:border-gold-400 outline-none">
                    <select id="new-folder-parent" class="flex-1 min-w-[100px] px-3 py-2 border border-gray-200 rounded-lg text-sm focus:border-gold-400 outline-none">
                        <option value="">Ohne Elternordner</option>
                        @foreach($folders as $folder)
                        <option value="{{ $folder->id }}">{{ $folder->name }}</option>
                        @endforeach
                    </select>
                    <button onclick="createFolder()" class="px-4 py-2 bg-gold-400 hover:bg-gold-500 text-white text-sm rounded-lg transition-colors shrink-0">+</button>
                </div>

                {{-- Folder Tabs with delete --}}
                @if($folders->count() > 0)
                <div class="flex flex-wrap gap-2 mb-4">
                    <button onclick="filterPhotosByFolder(null)" class="folder-tab text-xs px-3 py-1.5 rounded-lg border border-gray-200 hover:border-gold-400 transition-colors {{ !request('folder') ? 'bg-gold-50 border-gold-400 text-gold-600' : '' }}" data-folder="">Alle Fotos</button>
                    @foreach($folders as $folder)
                    <div class="folder-tab-wrapper flex items-center gap-1">
                        <button onclick="filterPhotosByFolder({{ $folder->id }})" class="folder-tab text-xs px-3 py-1.5 rounded-lg border border-gray-200 hover:border-gold-400 transition-colors {{ request('folder') == $folder->id ? 'bg-gold-50 border-gold-400 text-gold-600' : '' }}" data-folder="{{ $folder->id }}">{{ $folder->name }}</button>
                        <button onclick="deleteFolder({{ $folder->id }}, '{{ addslashes($folder->name) }}')" class="w-5 h-5 text-red-400 hover:text-red-600 text-xs leading-none" title="Ordner löschen">&times;</button>
                    </div>
                    @endforeach
                </div>
                @endif>

                {{-- Bulk Delete Bar --}}
                <div id="bulk-bar" class="hidden mb-4 p-3 bg-red-50 border border-red-200 rounded-lg flex items-center justify-between">
                    <span class="text-sm text-red-600"><span id="selected-count">0</span> ausgewählt</span>
                    <div class="flex items-center gap-2">
                        <button onclick="clearSelection()" class="text-xs text-gray-500 hover:text-gray-700">Aufheben</button>
                        <button onclick="bulkDeletePhotos()" class="text-xs px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors">Ausgewählte löschen</button>
                    </div>
                </div>

                {{-- Upload Dropzone --}}
                <div id="dropzone" class="border-2 border-dashed border-gray-200 rounded-xl p-8 text-center mb-6 cursor-pointer hover:border-gold-300 transition-colors">
                    <svg class="w-10 h-10 mx-auto text-gold-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M17 8l-5-5-5 5M12 3v12"/>
                    </svg>
                    <p class="text-sm text-gray-500">Fotos hierher ziehen oder klicken</p>
                    <p class="text-xs text-gray-300 mt-1">JPG, PNG, WebP – max. 20MB</p>
                    <input type="file" id="file-input" multiple accept="image/jpeg,image/png,image/webp" class="hidden">
                </div>

                {{-- Photo Grid --}}
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3" id="photo-grid">
                    @foreach($photos as $photo)
                    <div class="relative aspect-square rounded-lg overflow-hidden bg-gray-100 group photo-item" data-id="{{ $photo->id }}" data-folder="{{ $photo->folder_id ?? 0 }}">
                        <div class="absolute top-2 left-2 z-10">
                            <input type="checkbox" class="photo-checkbox w-5 h-5 rounded border-gray-300 text-gold-400 focus:ring-gold-400 cursor-pointer" value="{{ $photo->id }}" onchange="updateBulkBar()">
                        </div>
                        <img src="/storage/projects/{{ $photo->filename }}" alt="{{ $photo->original_name }}" class="w-full h-full object-cover cursor-pointer" onclick="openAdminLightbox('{{ $photo->id }}')">
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/60 to-transparent p-2 flex items-end justify-between">
                            <span class="text-white text-xs truncate">{{ $photo->original_name }}</span>
                            <button onclick="deletePhoto({{ $photo->id }})" class="text-white/80 hover:text-red-400 text-lg leading-none shrink-0 ml-2">&times;</button>
                        </div>
                    </div>
                    @endforeach
                </div>

                @if($photos->isEmpty())
                <p class="text-center text-gray-300 text-sm py-8">Noch keine Fotos hochgeladen.</p>
                @endif
            </div>

            {{-- Right: Share Links --}}
            <div class="bg-white rounded-xl border border-gray-100 p-6">
                <h2 class="font-medium text-gray-700 mb-4">Share-Links</h2>

                {{-- Create Share --}}
                <div class="space-y-3 mb-6">
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="text-xs text-gray-400 mb-1 block">Ablauf (Tage)</label>
                            <input type="number" id="share-expiry" placeholder="Unbegrenzt" min="1" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:border-gold-400 outline-none">
                        </div>
                        <div>
                            <label class="text-xs text-gray-400 mb-1 block">Passwort</label>
                            <input type="text" id="share-password" placeholder="Optional" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:border-gold-400 outline-none">
                        </div>
                    </div>
                    <label class="flex items-center gap-2 text-sm text-gray-600">
                        <input type="checkbox" id="share-download" checked class="rounded text-gold-400">
                        Download erlaubt
                    </label>
                    <button onclick="createShare()" class="w-full py-2.5 bg-gold-400 hover:bg-gold-500 text-white text-sm font-medium rounded-xl transition-colors">
                        Share-Link erstellen
                    </button>
                </div>

                {{-- Existing Shares --}}
                <div class="space-y-3" id="share-list">
                    @foreach($shares as $share)
                    <div class="p-3 bg-gray-50 rounded-lg" data-id="{{ $share->id }}">
                        <code class="text-xs text-gold-500 block truncate">{{ $share->token }}</code>
                        <div class="flex items-center gap-2 mt-1 text-xs text-gray-400">
                            <span>{{ $share->access_count }} Aufrufe</span>
                            @if($share->expires_at)<span>Läuft ab: {{ $share->expires_at->format('d.m.Y') }}</span>@endif
                            @if($share->password_hash)<span>🔒</span>@endif
                        </div>
                        <div class="flex gap-2 mt-2">
                            <button onclick="copyShare('{{ $share->token }}')" class="text-xs px-2 py-1 bg-white border border-gray-200 rounded hover:border-gold-300 transition-colors">Kopieren</button>
                            <button onclick="deleteShare({{ $share->id }})" class="text-xs px-2 py-1 bg-white border border-gray-200 rounded text-red-400 hover:border-red-300 transition-colors">Löschen</button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- Admin Lightbox --}}
<div id="admin-lightbox" class="fixed inset-0 z-[9999] bg-black/95 hidden items-center justify-center p-4" aria-hidden="true">
    <button class="absolute top-4 right-4 text-white text-4xl hover:text-gold-400 transition-colors z-10" onclick="closeAdminLightbox()">&times;</button>
    <button class="absolute left-4 top-1/2 -translate-y-1/2 text-white text-3xl hover:text-gold-400 transition-colors hidden md:block" onclick="adminLbPrev()">&#10094;</button>
    <img id="admin-lightbox-img" src="" alt="" class="max-w-[90vw] max-h-[85vh] object-contain rounded-lg">
    <button class="absolute right-4 top-1/2 -translate-y-1/2 text-white text-3xl hover:text-gold-400 transition-colors hidden md:block" onclick="adminLbNext()">&#10095;</button>
</div>

@push('scripts')
<script>
    const PROJECT_ID = {{ $project->id }};

    // === UPLOAD ===
    const dropzone = document.getElementById('dropzone');
    const fileInput = document.getElementById('file-input');

    if (dropzone) {
        dropzone.addEventListener('click', () => fileInput.click());
        dropzone.addEventListener('dragover', (e) => { e.preventDefault(); dropzone.classList.add('border-gold-400'); });
        dropzone.addEventListener('dragleave', () => dropzone.classList.remove('border-gold-400'));
        dropzone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropzone.classList.remove('border-gold-400');
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

    function openAdminLightbox(photoId) {
        const idx = ADMIN_PHOTOS.findIndex(p => p.id == photoId);
        if (idx === -1) return;
        adminLightboxIndex = idx;
        const lb = document.getElementById('admin-lightbox');
        const lbImg = document.getElementById('admin-lightbox-img');
        lbImg.src = ADMIN_PHOTOS[adminLightboxIndex].filename;
        lb.classList.remove('hidden');
        lb.classList.add('flex');
    }

    function closeAdminLightbox() {
        const lb = document.getElementById('admin-lightbox');
        lb.classList.add('hidden');
        lb.classList.remove('flex');
    }

    function adminLbPrev() {
        adminLightboxIndex = (adminLightboxIndex - 1 + ADMIN_PHOTOS.length) % ADMIN_PHOTOS.length;
        document.getElementById('admin-lightbox-img').src = ADMIN_PHOTOS[adminLightboxIndex].filename;
    }

    function adminLbNext() {
        adminLightboxIndex = (adminLightboxIndex + 1) % ADMIN_PHOTOS.length;
        document.getElementById('admin-lightbox-img').src = ADMIN_PHOTOS[adminLightboxIndex].filename;
    }

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

    // === FOLDER FILTER ===
    function filterPhotosByFolder(folderId) {
        document.querySelectorAll('.photo-item').forEach(item => {
            if (folderId === null || item.dataset.folder == folderId) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
        document.querySelectorAll('.folder-tab').forEach(tab => {
            const isActive = folderId === null ? tab.dataset.folder === '' : tab.dataset.folder == folderId;
            tab.classList.toggle('bg-gold-50', isActive);
            tab.classList.toggle('border-gold-400', isActive);
            tab.classList.toggle('text-gold-600', isActive);
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
</script>
@endpush
