<?php

namespace Tests\Unit;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_project_can_be_created(): void
    {
        $project = Project::create([
            'name' => 'Test Project',
            'description' => 'Test Description',
            'slug' => 'test-project-abc123',
        ]);

        $this->assertDatabaseHas('projects', ['name' => 'Test Project']);
        $this->assertNotNull($project->id);
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
}
