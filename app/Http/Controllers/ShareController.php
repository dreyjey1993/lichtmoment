<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use App\Models\Photo;
use App\Models\Project;
use App\Models\ShareLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class ShareController extends Controller
{
    public function show($token)
    {
        $shareLink = ShareLink::where('token', $token)->first();

        if (!$shareLink) {
            return response()->view('share.error', ['message' => 'Dieser Link existiert nicht oder ist abgelaufen.'], 404);
        }

        if ($shareLink->isExpired()) {
            return response()->view('share.error', ['message' => 'Dieser Link ist abgelaufen.'], 410);
        }

        $shareLink->increment('access_count');

        $project = Project::findOrFail($shareLink->project_id);
        $photos = Photo::where('project_id', $project->id)
            ->orderBy('folder_id')
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->get();

        $folders = Folder::where('project_id', $project->id)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $photographer = [
            'name' => 'Markus Licht',
            'phone' => '+49 171 234 56 78',
            'email' => 'info@lichtmoment.de',
        ];

        $needsPassword = !empty($shareLink->password_hash);

        return view('share.gallery', compact(
            'project', 'photos', 'folders', 'shareLink',
            'photographer', 'needsPassword', 'token'
        ));
    }

    public function loadGallery(Request $request)
    {
        $request->validate(['token' => 'required|string']);

        $shareLink = ShareLink::where('token', $request->token)->first();

        if (!$shareLink) {
            return response()->json(['error' => 'Ungültiger Link'], 404);
        }

        $query = Photo::where('project_id', $shareLink->project_id);

        if ($request->filled('folder_id')) {
            $query->where('folder_id', $request->folder_id);
        }

        $photos = $query->orderBy('sort_order')->orderBy('created_at', 'desc')->get();

        return response()->json([
            'photos' => $photos,
            'download_enabled' => (bool)$shareLink->download_enabled,
        ]);
    }

    public function checkPassword(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'password' => 'required|string',
        ]);

        $shareLink = ShareLink::where('token', $request->token)->first();

        if (!$shareLink) {
            return response()->json(['error' => 'Ungültiger Link'], 404);
        }

        $passwordToCheck = $shareLink->password_hash ?: $shareLink->project->password_hash;

        if ($passwordToCheck && password_verify($request->password, $passwordToCheck)) {
            return response()->json(['success' => true]);
        }

        return response()->json(['error' => 'Falsches Passwort'], 401);
    }

    public function downloadPhoto($id, Request $request)
    {
        $token = $request->query('token');
        $shareLink = ShareLink::where('token', $token)->first();

        if (!$shareLink || !$shareLink->download_enabled) {
            abort(403);
        }

        $photo = Photo::where('id', $id)
            ->where('project_id', $shareLink->project_id)
            ->firstOrFail();

        $path = Storage::disk('public')->path('projects/' . $photo->filename);

        if (!file_exists($path)) {
            abort(404);
        }

        return response()->download($path, $photo->original_name);
    }

    public function downloadZip(Request $request)
    {
        $request->validate(['token' => 'required|string']);

        $shareLink = ShareLink::where('token', $request->token)->first();

        if (!$shareLink || !$shareLink->download_enabled) {
            abort(403);
        }

        $query = Photo::where('project_id', $shareLink->project_id);

        if (!empty($request->photo_ids)) {
            $query->whereIn('id', $request->photo_ids);
        }

        $photos = $query->get();

        $zip = new ZipArchive();
        $zipName = tempnam(sys_get_temp_dir(), 'share_') . '.zip';
        $zip->open($zipName, ZipArchive::CREATE);

        foreach ($photos as $photo) {
            $path = Storage::disk('public')->path('projects/' . $photo->filename);
            if (file_exists($path)) {
                $zip->addFile($path, $photo->original_name);
            }
        }

        $zip->close();

        return response()->download($zipName, 'fotos.zip')->deleteFileAfterSend();
    }
}
