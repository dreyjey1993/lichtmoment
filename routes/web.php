<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ShareController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Landing page
Route::get('/', [HomeController::class, 'index'])->name('home');

// Legal pages
Route::get('/impressum', [HomeController::class, 'impressum'])->name('impressum');
Route::get('/datenschutz', [HomeController::class, 'datenschutz'])->name('datenschutz');

// Share gallery (public)
Route::get('/share/{token}', [ShareController::class, 'show'])->name('share.show');
Route::post('/share/api/gallery', [ShareController::class, 'loadGallery'])->name('share.api.gallery');
Route::post('/share/api/check-password', [ShareController::class, 'checkPassword'])->name('share.api.check-password');
Route::get('/share/download/photo/{id}', [ShareController::class, 'downloadPhoto'])->name('share.download.photo');
Route::post('/share/download/zip', [ShareController::class, 'downloadZip'])->name('share.download.zip');

// Admin
Route::get('/admin/login', [AdminController::class, 'loginPage'])->name('admin.login');
Route::post('/admin/login', [AdminController::class, 'login'])->middleware('throttle:5,1');
Route::get('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');

Route::middleware('admin.auth')->group(function () {
    Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/project/new', [AdminController::class, 'newProject'])->name('admin.project.new');
    Route::post('/admin/project/create', [AdminController::class, 'createProject'])->name('admin.project.create');
    Route::get('/admin/project/{id}', [AdminController::class, 'projectDetail'])->name('admin.project.detail');

    // API
    Route::post('/admin/upload', [AdminController::class, 'uploadPhoto'])->name('admin.upload');
    Route::post('/admin/folder/create', [AdminController::class, 'createFolder'])->name('admin.folder.create');
    Route::post('/admin/share/create', [AdminController::class, 'createShareLink'])->name('admin.share.create');
    Route::post('/admin/project/{id}/settings', [AdminController::class, 'updateProjectSettings'])->name('admin.project.settings');
    Route::get('/admin/api/shares/{projectId}', [AdminController::class, 'getShareLinks'])->name('admin.api.shares');
    Route::post('/admin/api/delete', [AdminController::class, 'deleteItem'])->name('admin.api.delete');
    Route::post('/admin/api/bulk-delete-photos', [AdminController::class, 'bulkDeletePhotos'])->name('admin.api.bulk-delete-photos');
    Route::post('/admin/api/delete-all-photos', [AdminController::class, 'deleteAllPhotos'])->name('admin.api.delete-all-photos');
});
