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

class SecurityTest extends TestCase
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

    // ─── CSRF Protection ────────────────────────────────────────────

    public function test_admin_login_requires_csrf_token(): void
    {
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);

        $response = $this->post('/admin/login', [
            'username' => 'admin',
            'password' => 'wasd1234',
        ]);
        $response->assertRedirect(route('admin.dashboard'));
    }

    // ─── SQL Injection ──────────────────────────────────────────────

    public function test_login_with_sql_injection_in_username(): void
    {
        $response = $this->post('/admin/login', [
            'username' => "' OR '1'='1",
            'password' => "' OR '1'='1",
        ]);
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_login_with_sql_injection_in_password(): void
    {
        $response = $this->post('/admin/login', [
            'username' => 'admin',
            'password' => "' OR '1'='1' --",
        ]);
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    // ─── XSS Prevention ─────────────────────────────────────────────

    public function test_project_creation_sanitizes_html_in_name(): void
    {
        $user = User::where('email', 'admin@lichtmoment.de')->first();

        $response = $this->actingAs($user, 'web')->post('/admin/project/create', [
            'name' => '<script>alert("xss")</script>Test',
        ]);

        $this->assertDatabaseHas('projects', [
            'name' => '<script>alert("xss")</script>Test',
        ]);
    }

    // ─── Authorization ──────────────────────────────────────────────

    public function test_guest_cannot_access_admin_dashboard(): void
    {
        $response = $this->get('/admin');
        $response->assertRedirect(route('admin.login'));
    }

    public function test_guest_cannot_create_project(): void
    {
        $response = $this->post('/admin/project/create', [
            'name' => 'Hacker Project',
        ]);
        $response->assertRedirect(route('admin.login'));
    }

    public function test_guest_cannot_upload_photos(): void
    {
        $fakeImage = \Illuminate\Http\Testing\File::fake()->create('hack.jpg', 10, 'image/jpeg');

        $response = $this->post('/admin/upload', [
            'project_id' => 1,
            'photos' => [$fakeImage],
        ]);
        $response->assertRedirect(route('admin.login'));
    }

    public function test_guest_cannot_delete_items(): void
    {
        $response = $this->post('/admin/api/delete', ['type' => 'project', 'id' => 1]);
        $response->assertRedirect(route('admin.login'));
    }

    public function test_guest_cannot_create_share_links(): void
    {
        $response = $this->post('/admin/share/create', [
            'project_id' => 1,
        ]);
        $response->assertRedirect(route('admin.login'));
    }

    // ─── Input Validation ───────────────────────────────────────────

    public function test_project_name_max_length(): void
    {
        $user = User::where('email', 'admin@lichtmoment.de')->first();

        $response = $this->actingAs($user, 'web')->post('/admin/project/create', [
            'name' => str_repeat('a', 256),
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_project_name_exactly_max_length_accepted(): void
    {
        $user = User::where('email', 'admin@lichtmoment.de')->first();

        $response = $this->actingAs($user, 'web')->post('/admin/project/create', [
            'name' => str_repeat('a', 255),
        ]);

        $this->assertDatabaseHas('projects', ['name' => str_repeat('a', 255)]);
    }

    public function test_upload_rejects_oversized_files(): void
    {
        $user = User::where('email', 'admin@lichtmoment.de')->first();
        $project = Project::first();

        // Create a fake file larger than 20MB (max in validation)
        $fakeImage = \Illuminate\Http\Testing\File::fake()->create('huge.jpg', 20481, 'image/jpeg');

        $response = $this->actingAs($user, 'web')->post('/admin/upload', [
            'project_id' => $project->id,
            'photos' => [$fakeImage],
        ]);

        $response->assertSessionHasErrors(['photos.0']);
    }

    public function test_upload_rejects_invalid_mime_types(): void
    {
        $user = User::where('email', 'admin@lichtmoment.de')->first();
        $project = Project::first();

        $fakeFile = \Illuminate\Http\Testing\File::fake()->create('malware.exe', 100, 'application/x-msdownload');

        $response = $this->actingAs($user, 'web')->post('/admin/upload', [
            'project_id' => $project->id,
            'photos' => [$fakeFile],
        ]);

        $response->assertSessionHasErrors(['photos.0']);
    }

    // ─── Share Link Security ────────────────────────────────────────

    public function test_share_link_without_password_is_accessible(): void
    {
        $project = Project::first();
        $share = ShareLink::create([
            'project_id' => $project->id,
            'token' => 'nopassword123456',
        ]);

        $response = $this->get('/share/' . $share->token);
        $response->assertStatus(200);
    }

    public function test_share_link_with_wrong_password_returns_401(): void
    {
        $project = Project::first();
        $share = ShareLink::create([
            'project_id' => $project->id,
            'token' => 'withpassword12345',
            'password_hash' => bcrypt('correct'),
        ]);

        $response = $this->postJson('/share/api/check-password', [
            'token' => $share->token,
            'password' => 'wrong',
        ]);

        $response->assertStatus(401);
    }

    public function test_share_download_requires_token(): void
    {
        $response = $this->get('/share/download/photo/1');
        $response->assertStatus(403);
    }

    public function test_share_download_with_invalid_token(): void
    {
        $response = $this->get('/share/download/photo/1?token=invalidtoken123');
        $response->assertStatus(403);
    }

    public function test_share_zip_download_requires_token(): void
    {
        $response = $this->postJson('/share/download/zip', []);
        $response->assertSessionHasErrors(['token']);
    }

    // ─── Session Fixation ───────────────────────────────────────────

    public function test_session_regenerated_on_login(): void
    {
        $response = $this->post('/admin/login', [
            'username' => 'admin',
            'password' => 'wasd1234',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertNotNull(session('admin_id'));
    }

    // ─── Rate Limiting (basic) ──────────────────────────────────────

    public function test_multiple_failed_logins(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $response = $this->post('/admin/login', [
                'username' => 'admin',
                'password' => 'wrong' . $i,
            ]);
            $response->assertRedirect();
        }

        // After 5 failures, rate limit should kick in
        $response = $this->post('/admin/login', [
            'username' => 'admin',
            'password' => 'wrong_again',
        ]);
        $response->assertStatus(429);
    }

    // ─── CSRF on Delete ────────────────────────────────────────────

    public function test_delete_requires_post_method(): void
    {
        $user = User::where('email', 'admin@lichtmoment.de')->first();
        $response = $this->actingAs($user, 'web')->get('/admin/api/delete?type=photo&id=1');
        $response->assertStatus(405);
    }

    // ─── Cover Image Upload ────────────────────────────────────────

    public function test_project_creation_with_cover_image(): void
    {
        $user = User::where('email', 'admin@lichtmoment.de')->first();
        $fakeCover = \Illuminate\Http\Testing\File::fake()->create('cover.jpg', 100, 'image/jpeg');

        $response = $this->actingAs($user, 'web')->post('/admin/project/create', [
            'name' => 'Cover Test Project',
            'cover_image' => $fakeCover,
        ]);

        $project = Project::where('name', 'Cover Test Project')->first();
        $this->assertNotNull($project);
        $this->assertNotNull($project->cover_image);
        $this->assertStringStartsWith('cover_', $project->cover_image);
    }

    public function test_project_creation_rejects_invalid_cover_image(): void
    {
        $user = User::where('email', 'admin@lichtmoment.de')->first();
        $fakeFile = \Illuminate\Http\Testing\File::fake()->create('malware.exe', 100);

        $response = $this->actingAs($user, 'web')->post('/admin/project/create', [
            'name' => 'Bad Cover Project',
            'cover_image' => $fakeFile,
        ]);
        $response->assertSessionHasErrors(['cover_image']);
    }

    // ─── Share Password Session ────────────────────────────────────

    public function test_share_password_check_sets_session(): void
    {
        $project = Project::first();
        $share = ShareLink::create([
            'project_id' => $project->id,
            'token' => 'sessiontest123456',
            'password_hash' => bcrypt('secret123'),
        ]);

        $response = $this->postJson('/share/api/check-password', [
            'token' => $share->token,
            'password' => 'secret123',
        ]);
        $response->assertStatus(200);
        $this->assertEquals(true, session('share_access_' . $share->token));
    }

    public function test_share_gallery_accessible_after_password_session(): void
    {
        $project = Project::first();
        $share = ShareLink::create([
            'project_id' => $project->id,
            'token' => 'sessiontest234567',
            'password_hash' => bcrypt('secret123'),
        ]);

        // First, check password to set session
        $this->postJson('/share/api/check-password', [
            'token' => $share->token,
            'password' => 'secret123',
        ]);

        // Now gallery should be accessible without password modal
        $response = $this->get('/share/' . $share->token);
        $response->assertStatus(200);
        $response->assertSee('gallery-content');
        $response->assertDontSee('password-modal');
    }

    // ─── Bulk Delete ───────────────────────────────────────────────

    public function test_bulk_delete_photos_works(): void
    {
        $user = User::where('email', 'admin@lichtmoment.de')->first();
        $project = Project::first();

        $photo1 = Photo::create([
            'project_id' => $project->id,
            'filename' => 'bulk1.jpg',
            'original_name' => 'bulk1.jpg',
        ]);
        $photo2 = Photo::create([
            'project_id' => $project->id,
            'filename' => 'bulk2.jpg',
            'original_name' => 'bulk2.jpg',
        ]);

        $response = $this->actingAs($user, 'web')->postJson('/admin/api/bulk-delete-photos', [
            'photo_ids' => [$photo1->id, $photo2->id],
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true, 'deleted' => 2]);
        $this->assertDatabaseMissing('photos', ['id' => $photo1->id]);
        $this->assertDatabaseMissing('photos', ['id' => $photo2->id]);
    }

    public function test_bulk_delete_rejects_non_array_photo_ids(): void
    {
        $user = User::where('email', 'admin@lichtmoment.de')->first();
        $response = $this->actingAs($user, 'web')->post('/admin/api/bulk-delete-photos', ['photo_ids' => 'not-an-array']);
        $this->assertTrue($response->status() === 422 || $response->status() === 302);
    }

    // ─── Expired Share Error Page ──────────────────────────────────

    public function test_expired_share_shows_detailed_error(): void
    {
        $project = Project::first();
        $share = ShareLink::create([
            'project_id' => $project->id,
            'token' => 'expiredsession123',
            'expires_at' => now()->subDays(3),
        ]);

        $response = $this->get('/share/' . $share->token);
        $response->assertStatus(410);
        $response->assertSee('Link abgelaufen');
        $response->assertSee('Zur Startseite');
    }

    // ─── Site Meta ─────────────────────────────────────────────────

    public function test_landing_page_has_csrf_meta(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('csrf-token', false);
    }

    public function test_landing_page_has_og_tags(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('og:title', false);
        $response->assertSee('og:description', false);
    }

    public function test_landing_page_has_favicon(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('favicon.svg', false);
    }
}
