<?php

namespace Tests\Unit;

use App\Models\Folder;
use App\Models\Photo;
use App\Models\Project;
use App\Models\ShareLink;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelsTest extends TestCase
{
    use RefreshDatabase;

    // ─── Project ────────────────────────────────────────────────────

    public function test_project_can_be_created(): void
    {
        $project = Project::create([
            'name' => 'Test Project',
            'description' => 'Test Description',
            'slug' => 'test-project-abc123',
        ]);

        $this->assertDatabaseHas('projects', ['name' => 'Test Project']);
    }

    public function test_project_has_fillable_attributes(): void
    {
        $project = new Project();
        $expected = ['name', 'description', 'slug', 'cover_image', 'download_enabled', 'password_hash'];
        $this->assertEquals($expected, $project->getFillable());
    }

    public function test_project_has_many_photos(): void
    {
        $project = Project::create(['name' => 'Test', 'slug' => 'test-abc123']);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $project->photos());
    }

    public function test_project_has_many_folders(): void
    {
        $project = Project::create(['name' => 'Test', 'slug' => 'test-def456']);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $project->folders());
    }

    public function test_project_has_many_share_links(): void
    {
        $project = Project::create(['name' => 'Test', 'slug' => 'test-ghi789']);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $project->shareLinks());
    }

    // ─── Photo ──────────────────────────────────────────────────────

    public function test_photo_can_be_created(): void
    {
        $project = Project::create(['name' => 'Test', 'slug' => 'test-abc123']);

        $photo = Photo::create([
            'project_id' => $project->id,
            'filename' => 'test.jpg',
            'original_name' => 'Test Image',
            'file_size' => 1024,
            'sort_order' => 1,
        ]);

        $this->assertDatabaseHas('photos', ['filename' => 'test.jpg']);
    }

    public function test_photo_has_fillable_attributes(): void
    {
        $photo = new Photo();
        $expected = ['project_id', 'folder_id', 'filename', 'original_name', 'file_size', 'sort_order'];
        $this->assertEquals($expected, $photo->getFillable());
    }

    public function test_photo_belongs_to_project(): void
    {
        $project = Project::create(['name' => 'Test', 'slug' => 'test-abc123']);
        $photo = Photo::create([
            'project_id' => $project->id,
            'filename' => 'test.jpg',
            'original_name' => 'Test',
        ]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $photo->project());
        $this->assertEquals($project->id, $photo->project->id);
    }

    public function test_photo_belongs_to_folder(): void
    {
        $project = Project::create(['name' => 'Test', 'slug' => 'test-abc123']);
        $folder = Folder::create(['project_id' => $project->id, 'name' => 'Test Folder']);
        $photo = Photo::create([
            'project_id' => $project->id,
            'folder_id' => $folder->id,
            'filename' => 'test.jpg',
            'original_name' => 'Test',
        ]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $photo->folder());
        $this->assertEquals($folder->id, $photo->folder->id);
    }

    public function test_photo_can_have_null_project(): void
    {
        $photo = Photo::create([
            'project_id' => null,
            'filename' => 'portfolio/test.jpg',
            'original_name' => 'Portfolio Image',
        ]);

        $this->assertNull($photo->project_id);
    }

    // ─── Folder ─────────────────────────────────────────────────────

    public function test_folder_can_be_created(): void
    {
        $project = Project::create(['name' => 'Test', 'slug' => 'test-abc123']);

        $folder = Folder::create([
            'project_id' => $project->id,
            'name' => 'Test Folder',
            'sort_order' => 1,
        ]);

        $this->assertDatabaseHas('folders', ['name' => 'Test Folder']);
    }

    public function test_folder_has_fillable_attributes(): void
    {
        $folder = new Folder();
        $expected = ['project_id', 'name', 'parent_id', 'sort_order'];
        $this->assertEquals($expected, $folder->getFillable());
    }

    public function test_folder_belongs_to_project(): void
    {
        $project = Project::create(['name' => 'Test', 'slug' => 'test-abc123']);
        $folder = Folder::create(['project_id' => $project->id, 'name' => 'Test']);

        $this->assertEquals($project->id, $folder->project->id);
    }

    // ─── ShareLink ──────────────────────────────────────────────────

    public function test_share_link_can_be_created(): void
    {
        $project = Project::create(['name' => 'Test', 'slug' => 'test-abc123']);

        $share = ShareLink::create([
            'project_id' => $project->id,
            'token' => 'testtoken12345678',
            'download_enabled' => true,
        ]);

        $this->assertDatabaseHas('share_links', ['token' => 'testtoken12345678']);
    }

    public function test_share_link_has_fillable_attributes(): void
    {
        $share = new ShareLink();
        $expected = ['project_id', 'token', 'password_hash', 'download_enabled', 'expires_at', 'access_count'];
        $this->assertEquals($expected, $share->getFillable());
    }

    public function test_share_link_belongs_to_project(): void
    {
        $project = Project::create(['name' => 'Test', 'slug' => 'test-abc123']);
        $share = ShareLink::create([
            'project_id' => $project->id,
            'token' => 'testtoken12345678',
        ]);

        $this->assertEquals($project->id, $share->project->id);
    }

    public function test_share_link_is_expired_returns_true_for_past_date(): void
    {
        $project = Project::create(['name' => 'Test', 'slug' => 'test-abc123']);
        $share = ShareLink::create([
            'project_id' => $project->id,
            'token' => 'expiredtoken1234',
            'expires_at' => now()->subDay(),
        ]);

        $this->assertTrue($share->isExpired());
    }

    public function test_share_link_is_expired_returns_false_for_future_date(): void
    {
        $project = Project::create(['name' => 'Test', 'slug' => 'test-abc123']);
        $share = ShareLink::create([
            'project_id' => $project->id,
            'token' => 'validtoken123456',
            'expires_at' => now()->addDays(7),
        ]);

        $this->assertFalse($share->isExpired());
    }

    public function test_share_link_is_expired_returns_false_for_null_expiry(): void
    {
        $project = Project::create(['name' => 'Test', 'slug' => 'test-abc123']);
        $share = ShareLink::create([
            'project_id' => $project->id,
            'token' => 'neverexpiretoken',
            'expires_at' => null,
        ]);

        $this->assertFalse($share->isExpired());
    }

    // ─── User ───────────────────────────────────────────────────────

    public function test_user_can_be_created(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    public function test_user_has_fillable_attributes(): void
    {
        $user = new User();
        $expected = ['name', 'email', 'password'];
        $this->assertEquals($expected, $user->getFillable());
    }

    public function test_user_password_is_hashed(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $this->assertNotEquals('password123', $user->password);
        $this->assertTrue(password_verify('password123', $user->password));
    }

    public function test_user_hidden_attributes(): void
    {
        $user = new User();
        $this->assertContains('password', $user->getHidden());
        $this->assertContains('remember_token', $user->getHidden());
    }
}
