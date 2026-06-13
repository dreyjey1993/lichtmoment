<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\ShareLink;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShareLinkFactory extends Factory
{
    protected $model = ShareLink::class;

    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'token' => fake()->sha256,
            'password_hash' => null,
            'download_enabled' => true,
            'expires_at' => null,
            'access_count' => 0,
        ];
    }
}
