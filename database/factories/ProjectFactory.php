<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        $name = fake()->firstName . ' & ' . fake()->lastName . ' – ' . fake()->randomElement(['Hochzeit', 'Trauung', 'Feier']);
        return [
            'name' => $name,
            'description' => fake()->sentence(10),
            'slug' => fake()->unique()->slug,
            'cover_image' => '',
            'download_enabled' => true,
            'password_hash' => null,
        ];
    }
}
