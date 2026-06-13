<?php

namespace Tests\Unit;

use App\Models\Photo;
use App\Models\Project;
use App\Models\Folder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhotoModelTest extends TestCase
{
    use RefreshDatabase;

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
}
