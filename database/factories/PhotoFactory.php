<?php

namespace Database\Factories;

use App\Models\Photo;
use Illuminate\Database\Eloquent\Factories\Factory;

class PhotoFactory extends Factory
{
    protected $model = Photo::class;

    public function definition(): array
    {
        return [
            'project_id' => \App\Models\Project::factory(),
            'folder_id' => null,
            'filename' => 'projects/' . fake()->uuid . '.jpg',
            'original_name' => fake()->word . '.jpg',
            'file_size' => fake()->numberBetween(1000000, 5000000),
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }
}
