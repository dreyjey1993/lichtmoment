<?php

namespace Tests\Feature;

use App\Models\Folder;
use App\Models\Photo;
use App\Models\Project;
use App\Models\ShareLink;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class LichtmomentTest extends TestCase
{
    use RefreshDatabase;

    protected function refreshTestDatabase(): void
    {
        Schema::dropIfExists('share_links');
        Schema::dropIfExists('photos');
        Schema::dropIfExists('folders');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('migrations');

        $this->artisan('migrate', ['--force' => true]);
        $this->artisan('db:seed', ['--force' => true]);
    }

    // ─── Landing Page ────────────────────────────────────────────────

    public function test_landing_page_returns_200(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Lichtmoment');
        $response->assertSee('Hochzeitsfotografie');
    }

    public function test_landing_page_contains_portfolio_section(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Portfolio');
    }

    public function test_landing_page_contains_about_section(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Markus Knuth');
    }

    public function test_landing_page_contains_footer_links(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Impressum');
        $response->assertSee('Datenschutz');
        $response->assertSee('Admin');
    }

    public function test_landing_page_contains_threejs_canvas(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('hero-canvas');
    }

    // ─── Static Pages ───────────────────────────────────────────────

    public function test_impressum_page_returns_200(): void
    {
        $response = $this->get('/impressum');
        $response->assertStatus(200);
        $response->assertSee('Impressum');
    }

    public function test_datenschutz_page_returns_200(): void
    {
        $response = $this->get('/datenschutz');
        $response->assertStatus(200);
        $response->assertSee('Datenschutzerklärung');
    }

    // ─── Admin Auth ─────────────────────────────────────────────────

    public function test_admin_login_page_returns_200(): void
    {
        $response = $this->get('/admin/login');
        $response->assertStatus(200);
    }

    public function test_admin_login_works(): void
    {
        $response = $this->post('/admin/login', [
            'username' => 'admin',
            'password' => 'wasd1234',
        ]);
        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_admin_login_fails_with_wrong_password(): void
    {
        $response = $this->post('/admin/login', [
            'username' => 'admin',
            'password' => 'wrongpassword',
        ]);
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_admin_login_fails_with_empty_credentials(): void
    {
        $response = $this->post('/admin/login', [
            'username' => '',
            'password' => '',
        ]);
        $response->assertSessionHasErrors(['username', 'password']);
    }

    public function test_admin_dashboard_requires_auth(): void
    {
        $response = $this->get('/admin');
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_dashboard_shows_projects(): void
    {
        $user = User::where('email', 'admin@lichtmoment.de')->first();
        $this->assertNotNull($user);

        $response = $this->actingAs($user, 'web')->get('/admin');
        $response->assertStatus(200);
    }

    public function test_admin_logout_works(): void
    {
        $user = User::where('email', 'admin@lichtmoment.de')->first();
        $this->actingAs($user, 'web');

        $response = $this->get('/admin/logout');
        $response->assertRedirect(route('admin.login'));
    }

    // ─── Project CRUD ───────────────────────────────────────────────

    public function test_project_creation(): void
    {
        $user = User::where('email', 'admin@lichtmoment.de')->first();

        $response = $this->actingAs($user, 'web')->post('/admin/project/create', [
            'name' => 'Test Hochzeit',
            'description' => 'Eine Test-Hochzeit',
        ]);

        $this->assertDatabaseHas('projects', ['name' => 'Test Hochzeit']);
        $response->assertRedirect();
    }

    public function test_project_creation_requires_name(): void
    {
        $user = User::where('email', 'admin@lichtmoment.de')->first();

        $response = $this->actingAs($user, 'web')->post('/admin/project/create', [
            'name' => '',
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_project_creation_creates_default_folder(): void
    {
        $user = User::where('email', 'admin@lichtmoment.de')->first();

        $response = $this->actingAs($user, 'web')->post('/admin/project/create', [
            'name' => 'Folder Test Project',
        ]);

        $project = Project::where('name', 'Folder Test Project')->first();
        $this->assertNotNull($project);
        $this->assertDatabaseHas('folders', [
            'project_id' => $project->id,
            'name' => 'Alle Fotos',
        ]);
    }

    public function test_project_detail_page_returns_200(): void
    {
        $user = User::where('email', 'admin@lichtmoment.de')->first();
        $project = Project::first();
        $this->assertNotNull($project);

        $response = $this->actingAs($user, 'web')->get('/admin/project/' . $project->id);
        $response->assertStatus(200);
    }

    public function test_project_detail_shows_photos(): void
    {
        $user = User::where('email', 'admin@lichtmoment.de')->first();
        $project = Project::first();

        $response = $this->actingAs($user, 'web')->get('/admin/project/' . $project->id);
        $response->assertStatus(200);
        $response->assertSee($project->name);
    }

    public function test_new_project_page_returns_200(): void
    {
        $user = User::where('email', 'admin@lichtmoment.de')->first();

        $response = $this->actingAs($user, 'web')->get('/admin/project/new');
        $response->assertStatus(200);
    }

    // ─── Photo Upload ───────────────────────────────────────────────

    public function test_photo_upload_works(): void
    {
        $user = User::where('email', 'admin@lichtmoment.de')->first();
        $project = Project::first();

        $fakeImage = \Illuminate\Http\Testing\File::fake()->create('test.jpg', 100, 'image/jpeg');

        $response = $this->actingAs($user, 'web')->post('/admin/upload', [
            'project_id' => $project->id,
            'photos' => [$fakeImage],
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    public function test_photo_upload_requires_project_id(): void
    {
        $user = User::where('email', 'admin@lichtmoment.de')->first();

        $fakeImage = \Illuminate\Http\Testing\File::fake()->create('test.jpg', 100, 'image/jpeg');

        $response = $this->actingAs($user, 'web')->post('/admin/upload', [
            'photos' => [$fakeImage],
        ]);

        $response->assertSessionHasErrors(['project_id']);
    }

    public function test_photo_upload_rejects_non_images(): void
    {
        $user = User::where('email', 'admin@lichtmoment.de')->first();
        $project = Project::first();

        $fakeFile = \Illuminate\Http\Testing\File::fake()->create('test.pdf', 100);

        $response = $this->actingAs($user, 'web')->post('/admin/upload', [
            'project_id' => $project->id,
            'photos' => [$fakeFile],
        ]);

        $response->assertSessionHasErrors(['photos.0']);
    }

    // ─── Folder CRUD ────────────────────────────────────────────────

    public function test_folder_creation(): void
    {
        $user = User::where('email', 'admin@lichtmoment.de')->first();
        $project = Project::first();

        $response = $this->actingAs($user, 'web')->post('/admin/folder/create', [
            'project_id' => $project->id,
            'name' => 'Test Folder',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('folders', [
            'project_id' => $project->id,
            'name' => 'Test Folder',
        ]);
    }

    public function test_folder_creation_requires_name(): void
    {
        $user = User::where('email', 'admin@lichtmoment.de')->first();
        $project = Project::first();

        $response = $this->actingAs($user, 'web')->post('/admin/folder/create', [
            'project_id' => $project->id,
            'name' => '',
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    // ─── Share Links ────────────────────────────────────────────────

    public function test_share_gallery_returns_200(): void
    {
        $project = Project::first();
        $this->assertNotNull($project);

        $share = ShareLink::create([
            'project_id' => $project->id,
            'token' => 'testshare123456789',
            'download_enabled' => true,
        ]);

        $response = $this->get('/share/' . $share->token);
        $response->assertStatus(200);
    }

    public function test_share_gallery_404_for_invalid_token(): void
    {
        $response = $this->get('/share/invalidtoken123456');
        $response->assertStatus(404);
    }

    public function test_share_gallery_blocks_expired_links(): void
    {
        $project = Project::first();
        $share = ShareLink::create([
            'project_id' => $project->id,
            'token' => 'expired1234567890',
            'expires_at' => now()->subDay(),
        ]);

        $response = $this->get('/share/' . $share->token);
        $response->assertStatus(410);
    }

    public function test_share_link_creation(): void
    {
        $user = User::where('email', 'admin@lichtmoment.de')->first();
        $project = Project::first();

        $response = $this->actingAs($user, 'web')->post('/admin/share/create', [
            'project_id' => $project->id,
            'download_enabled' => true,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('share_links', ['project_id' => $project->id]);
    }

    public function test_share_link_creation_requires_project_id(): void
    {
        $user = User::where('email', 'admin@lichtmoment.de')->first();

        $response = $this->actingAs($user, 'web')->post('/admin/share/create', [
            'download_enabled' => true,
        ]);

        $response->assertSessionHasErrors(['project_id']);
    }

    public function test_share_link_with_password(): void
    {
        $user = User::where('email', 'admin@lichtmoment.de')->first();
        $project = Project::first();

        $response = $this->actingAs($user, 'web')->post('/admin/share/create', [
            'project_id' => $project->id,
            'password' => 'secret123',
        ]);

        $response->assertStatus(200);
        $share = ShareLink::where('project_id', $project->id)->first();
        $this->assertNotNull($share->password_hash);
    }

    public function test_share_link_with_expiry(): void
    {
        $user = User::where('email', 'admin@lichtmoment.de')->first();
        $project = Project::first();

        $response = $this->actingAs($user, 'web')->post('/admin/share/create', [
            'project_id' => $project->id,
            'expires_days' => 7,
        ]);

        $response->assertStatus(200);
        $share = ShareLink::where('project_id', $project->id)->first();
        $this->assertNotNull($share->expires_at);
    }

    // ─── Project Settings ───────────────────────────────────────────

    public function test_project_settings_update(): void
    {
        $user = User::where('email', 'admin@lichtmoment.de')->first();
        $project = Project::first();

        $response = $this->actingAs($user, 'web')->post('/admin/project/' . $project->id . '/settings', [
            'download_enabled' => 0,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'download_enabled' => false,
        ]);
    }

    public function test_project_settings_update_description(): void
    {
        $user = User::where('email', 'admin@lichtmoment.de')->first();
        $project = Project::first();

        $response = $this->actingAs($user, 'web')->post('/admin/project/' . $project->id . '/update', [
            'description' => 'Updated description',
        ]);

        $response->assertStatus(302);
        $project->refresh();
        $this->assertEquals('Updated description', $project->description);
    }

    // ─── Delete Operations ──────────────────────────────────────────

    public function test_delete_photo(): void
    {
        $user = User::where('email', 'admin@lichtmoment.de')->first();
        $project = Project::first();
        $photo = Photo::where('project_id', $project->id)->first();
        $this->assertNotNull($photo);

        $response = $this->actingAs($user, 'web')->post('/admin/api/delete', ['type' => 'photo', 'id' => $photo->id]);
        $response->assertStatus(200);
        $this->assertDatabaseMissing('photos', ['id' => $photo->id]);
    }

    public function test_delete_folder(): void
    {
        $user = User::where('email', 'admin@lichtmoment.de')->first();
        $project = Project::first();
        $folder = Folder::where('project_id', $project->id)->first();
        $this->assertNotNull($folder);

        $response = $this->actingAs($user, 'web')->post('/admin/api/delete', ['type' => 'folder', 'id' => $folder->id]);
        $response->assertStatus(200);
        $this->assertDatabaseMissing('folders', ['id' => $folder->id]);
    }

    public function test_delete_share_link(): void
    {
        $user = User::where('email', 'admin@lichtmoment.de')->first();
        $project = Project::first();
        $share = ShareLink::create([
            'project_id' => $project->id,
            'token' => 'deletetest1234567',
        ]);

        $response = $this->actingAs($user, 'web')->post('/admin/api/delete', ['type' => 'share', 'id' => $share->id]);
        $response->assertStatus(200);
        $this->assertDatabaseMissing('share_links', ['id' => $share->id]);
    }

    public function test_delete_project_cascades(): void
    {
        $user = User::where('email', 'admin@lichtmoment.de')->first();

        $response = $this->actingAs($user, 'web')->post('/admin/project/create', [
            'name' => 'Cascade Test',
        ]);
        $project = Project::where('name', 'Cascade Test')->first();
        $this->assertNotNull($project);

        $photo = Photo::create([
            'project_id' => $project->id,
            'filename' => 'test.jpg',
            'original_name' => 'test.jpg',
        ]);
        $share = ShareLink::create([
            'project_id' => $project->id,
            'token' => 'cascadetest12345',
        ]);

        $response = $this->actingAs($user, 'web')->post('/admin/api/delete', ['type' => 'project', 'id' => $project->id]);
        $response->assertStatus(200);
        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
        $this->assertDatabaseMissing('photos', ['project_id' => $project->id]);
        $this->assertDatabaseMissing('share_links', ['project_id' => $project->id]);
    }

    public function test_delete_requires_id(): void
    {
        $user = User::where('email', 'admin@lichtmoment.de')->first();

        $response = $this->actingAs($user, 'web')->post('/admin/api/delete', ['type' => 'photo', 'id' => '']);
        $response->assertStatus(400);
    }

    // ─── Share API ──────────────────────────────────────────────────

    public function test_share_api_gallery(): void
    {
        $project = Project::first();
        $share = ShareLink::create([
            'project_id' => $project->id,
            'token' => 'apitest1234567890',
            'download_enabled' => true,
        ]);

        $response = $this->postJson('/share/api/gallery', [
            'token' => $share->token,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['photos', 'download_enabled']);
    }

    public function test_share_api_gallery_invalid_token(): void
    {
        $response = $this->postJson('/share/api/gallery', [
            'token' => 'nonexistenttoken123',
        ]);

        $response->assertStatus(404);
    }

    public function test_share_api_check_password_correct(): void
    {
        $project = Project::first();
        $share = ShareLink::create([
            'project_id' => $project->id,
            'token' => 'pwdtest1234567890',
            'password_hash' => bcrypt('secret123'),
        ]);

        $response = $this->postJson('/share/api/check-password', [
            'token' => $share->token,
            'password' => 'secret123',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    public function test_share_api_check_password_wrong(): void
    {
        $project = Project::first();
        $share = ShareLink::create([
            'project_id' => $project->id,
            'token' => 'pwdtest2234567890',
            'password_hash' => bcrypt('secret123'),
        ]);

        $response = $this->postJson('/share/api/check-password', [
            'token' => $share->token,
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401);
    }

    // ─── Share Links List API ───────────────────────────────────────

    public function test_get_share_links_api(): void
    {
        $user = User::where('email', 'admin@lichtmoment.de')->first();
        $project = Project::first();

        $response = $this->actingAs($user, 'web')->getJson('/admin/api/shares/' . $project->id);
        $response->assertStatus(200);
        $response->assertJsonStructure(['shares']);
    }

    // ─── Access Count ───────────────────────────────────────────────

    public function test_share_link_increments_access_count(): void
    {
        $project = Project::first();
        $share = ShareLink::create([
            'project_id' => $project->id,
            'token' => 'accesstest1234567',
            'access_count' => 0,
        ]);

        $this->get('/share/' . $share->token);

        $share->refresh();
        $this->assertEquals(1, $share->access_count);
    }

    // ─── Edge Cases ─────────────────────────────────────────────────

    public function test_project_detail_404_for_invalid_id(): void
    {
        $user = User::where('email', 'admin@lichtmoment.de')->first();

        $response = $this->actingAs($user, 'web')->get('/admin/project/99999');
        $response->assertStatus(404);
    }

    public function test_delete_nonexistent_photo_returns_success(): void
    {
        $user = User::where('email', 'admin@lichtmoment.de')->first();

        $response = $this->actingAs($user, 'web')->post('/admin/api/delete', ['type' => 'photo', 'id' => 99999]);
        $response->assertStatus(200);
    }

    public function test_admin_routes_require_middleware(): void
    {
        $adminRoutes = [
            'GET /admin',
            'GET /admin/project/new',
            'GET /admin/project/1',
            'POST /admin/upload',
            'POST /admin/folder/create',
            'POST /admin/share/create',
            'POST /admin/project/1/settings',
            'POST /admin/project/1/update',
            'POST /admin/api/delete',
            'GET /admin/api/shares/1',
        ];

        foreach ($adminRoutes as $route) {
            $parts = explode(' ', $route);
            $method = strtolower($parts[0]);
            $url = $parts[1];

            $response = $this->{$method}($url);
            $this->assertTrue(
                $response->isRedirection() && $response->headers->get('Location') === route('admin.login'),
                "Route {$route} should redirect to login"
            );
        }
    }

    // ─── Cover Image Update ──────────────────────────────────────────

    public function test_project_cover_image_update(): void
    {
        $user = User::where('email', 'admin@lichtmoment.de')->first();
        $project = Project::first();

        $fakeImage = \Illuminate\Http\Testing\File::fake()->create('cover.jpg', 100, 'image/jpeg');

        $response = $this->actingAs($user, 'web')->post('/admin/project/' . $project->id . '/update', [
            'cover_image' => $fakeImage,
        ]);

        $response->assertRedirect(route('admin.project.detail', $project->id));
        $project->refresh();
        $this->assertNotNull($project->cover_image);
        $this->assertStringStartsWith('cover_', $project->cover_image);
    }

    public function test_project_cover_image_remove(): void
    {
        $user = User::where('email', 'admin@lichtmoment.de')->first();
        $project = Project::first();

        // Set a cover image first
        $fakeImage = \Illuminate\Http\Testing\File::fake()->create('cover.jpg', 100, 'image/jpeg');
        $this->actingAs($user, 'web')->post('/admin/project/' . $project->id . '/update', [
            'cover_image' => $fakeImage,
        ]);
        $project->refresh();
        $this->assertNotNull($project->cover_image);

        // Now remove it
        $response = $this->actingAs($user, 'web')->post('/admin/project/' . $project->id . '/update', [
            'remove_cover' => '1',
        ]);

        $response->assertRedirect(route('admin.project.detail', $project->id));
        $project->refresh();
        $this->assertNull($project->cover_image);
    }

    public function test_project_update_requires_valid_cover_image(): void
    {
        $user = User::where('email', 'admin@lichtmoment.de')->first();
        $project = Project::first();

        $fakeFile = \Illuminate\Http\Testing\File::fake()->create('cover.pdf', 100);

        $response = $this->actingAs($user, 'web')->post('/admin/project/' . $project->id . '/update', [
            'cover_image' => $fakeFile,
        ]);

        $response->assertSessionHasErrors(['cover_image']);
    }

    // ─── Download Per Share Link ────────────────────────────────────

    public function test_share_link_download_enabled(): void
    {
        $project = Project::first();
        $share = ShareLink::create([
            'project_id' => $project->id,
            'token' => 'dltest12345678901',
            'download_enabled' => true,
        ]);

        $response = $this->get('/share/' . $share->token);
        $response->assertStatus(200);
        $response->assertSee('Alle Fotos herunterladen');
    }

    public function test_share_link_download_disabled(): void
    {
        $project = Project::first();
        $share = ShareLink::create([
            'project_id' => $project->id,
            'token' => 'ndltest1234567890',
            'download_enabled' => false,
        ]);

        $response = $this->get('/share/' . $share->token);
        $response->assertStatus(200);
        $response->assertDontSee('Alle Fotos herunterladen');
    }

    public function test_share_download_zip_requires_download_enabled(): void
    {
        $project = Project::first();
        $share = ShareLink::create([
            'project_id' => $project->id,
            'token' => 'nodltest12345678',
            'download_enabled' => false,
        ]);

        $response = $this->postJson('/share/download/zip', [
            'token' => $share->token,
        ]);

        $response->assertStatus(403);
    }

    public function test_share_download_photo_requires_download_enabled(): void
    {
        $project = Project::first();
        $share = ShareLink::create([
            'project_id' => $project->id,
            'token' => 'nodltest22345678',
            'download_enabled' => false,
        ]);
        $photo = Photo::where('project_id', $project->id)->first();
        $this->assertNotNull($photo);

        $response = $this->get('/share/download/photo/' . $photo->id . '?token=' . $share->token);
        $response->assertStatus(403);
    }

    // ─── Dashboard Shows Cover Image ────────────────────────────────

    public function test_dashboard_shows_cover_image_when_set(): void
    {
        $user = User::where('email', 'admin@lichtmoment.de')->first();
        $project = Project::first();

        $response = $this->actingAs($user, 'web')->get('/admin');
        $response->assertStatus(200);
        $response->assertSee($project->name);
    }

    public function test_dashboard_shows_initial_when_no_cover(): void
    {
        $user = User::where('email', 'admin@lichtmoment.de')->first();

        // Create project without cover
        $this->actingAs($user, 'web')->post('/admin/project/create', [
            'name' => 'No Cover Project',
        ]);

        $response = $this->actingAs($user, 'web')->get('/admin');
        $response->assertStatus(200);
        $response->assertSee('No Cover Project');
    }
}
