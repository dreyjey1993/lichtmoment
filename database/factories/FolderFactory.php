<?php

namespace Database\Factories;

use App\Models\Folder;
use Illuminate\Database\Eloquent\Factories\Factory;

class FolderFactory extends Factory
{
    protected $model = Folder::class;

    public function definition(): array
    {
        return [
            'project_id' => \App\Models\Project::factory(),
            'name' => fake()->randomElement(['Zeremonie', 'Empfang', 'Portraits', 'Details & Deko', 'Vorbereitung', 'Feier']),
            'parent_id' => null,
            'sort_order' => fake()->numberBetween(0, 10),
        ];
    }
}
