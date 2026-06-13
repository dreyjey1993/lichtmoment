<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use App\Models\Photo;
use App\Models\Project;
use App\Models\ShareLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function loginPage()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $user = \App\Models\User::where('email', 'admin@lichtmoment.de')->first();

        if ($user && Hash::check($request->password, $user->password)) {
            session(['admin_id' => $user->id]);
            return redirect()->route('admin.dashboard');
        }

        return back()->with('error', 'Ungültige Anmeldedaten.')->withInput();
    }

    public function logout()
    {
        session()->forget('admin_id');
        return redirect()->route('admin.login');
    }

    public function dashboard()
    {
        $projects = Project::withCount('photos')->orderBy('created_at', 'desc')->get();
        return view('admin.dashboard', compact('projects'));
    }

    public function newProject()
    {
        return view('admin.project', [
            'project' => null,
            'folders' => [],
            'photos' => [],
            'shares' => [],
        ]);
    }

    public function createProject(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);

        $project = Project::create([
            'name' => $request->name,
            'description' => $request->description ?? '',
            'slug' => Str::slug($request->name) . '-' . Str::random(6),
        ]);

        // Create default folder
        Folder::create([
            'project_id' => $project->id,
            'name' => 'Alle Fotos',
            'sort_order' => 0,
        ]);

        return redirect()->route('admin.project.detail', $project->id);
    }

    public function projectDetail($id)
    {
        $project = Project::findOrFail($id);
        $folders = Folder::where('project_id', $id)->orderBy('sort_order')->orderBy('name')->get();
        $photos = Photo::where('project_id', $id)->orderBy('folder_id')->orderBy('sort_order')->orderBy('created_at', 'desc')->get();
        $shares = ShareLink::where('project_id', $id)->orderBy('created_at', 'desc')->get();

        return view('admin.project', compact('project', 'folders', 'photos', 'shares'));
    }

    public function uploadPhoto(Request $request)
    {
        $request->validate([
            'project_id' => 'required|integer',
            'photos' => 'required|array',
            'photos.*' => 'file|mimes:jpg,jpeg,webp,png|max:20480',
        ]);

        $project = Project::findOrFail($request->project_id);
        $folderId = $request->folder_id ?: null;
        $results = [];

        foreach ($request->file('photos') as $file) {
            $filename = Str::random(16) . '.' . $file->extension();
            $path = $file->storeAs('projects', $filename, 'public');

            $photo = Photo::create([
                'project_id' => $project->id,
                'folder_id' => $folderId,
                'filename' => $filename,
                'original_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
            ]);

            $results[] = [
                'id' => $photo->id,
                'filename' => Storage::url('projects/' . $filename),
                'original_name' => $photo->original_name,
            ];
        }

        return response()->json(['success' => true, 'uploaded' => $results]);
    }

    public function createFolder(Request $request)
    {
        $request->validate([
            'project_id' => 'required|integer',
            'name' => 'required|string|max:255',
        ]);

        $folder = Folder::create([
            'project_id' => $request->project_id,
            'name' => $request->name,
            'parent_id' => $request->parent_id ?: null,
        ]);

        return response()->json(['success' => true, 'id' => $folder->id]);
    }

    public function createShareLink(Request $request)
    {
        $request->validate(['project_id' => 'required|integer']);

        $project = Project::findOrFail($request->project_id);
        $token = Str::random(32);
        $expiresAt = $request->expires_days ? now()->addDays((int)$request->expires_days) : null;
        $passwordHash = $request->password ? Hash::make($request->password) : null;

        $share = ShareLink::create([
            'project_id' => $project->id,
            'token' => $token,
            'password_hash' => $passwordHash,
            'download_enabled' => $request->has('download_enabled'),
            'expires_at' => $expiresAt,
        ]);

        return response()->json([
            'success' => true,
            'token' => $token,
            'url' => route('share.show', $token),
        ]);
    }

    public function updateProjectSettings(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        if ($request->has('download_enabled')) {
            $project->download_enabled = (bool)$request->download_enabled;
        }

        if ($request->has('password')) {
            $project->password_hash = $request->password ? Hash::make($request->password) : null;
        }

        if ($request->has('description')) {
            $project->description = $request->description;
        }

        $project->save();

        return response()->json(['success' => true]);
    }

    public function getShareLinks($projectId)
    {
        $shares = ShareLink::where('project_id', $projectId)
            ->select(['id', 'token', 'download_enabled', 'expires_at', 'access_count', 'created_at'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['shares' => $shares]);
    }

    public function deleteItem(Request $request)
    {
        $type = $request->query('type');
        $id = (int)$request->query('id');

        if (!$id) {
            return response()->json(['error' => 'ID fehlt'], 400);
        }

        switch ($type) {
            case 'photo':
                $photo = Photo::find($id);
                if ($photo) {
                    Storage::disk('public')->delete('projects/' . $photo->filename);
                    $photo->delete();
                }
                break;
            case 'folder':
                Folder::destroy($id);
                break;
            case 'share':
                ShareLink::destroy($id);
                break;
            case 'project':
                $project = Project::find($id);
                if ($project) {
                    foreach ($project->photos as $photo) {
                        Storage::disk('public')->delete('projects/' . $photo->filename);
                    }
                    $project->delete();
                }
                break;
        }

        return response()->json(['success' => true]);
    }
}
