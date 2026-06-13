<?php

namespace Database\Seeders;

use App\Models\Photo;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        if (!User::where('email', 'admin@lichtmoment.de')->exists()) {
            User::create([
                'name' => 'Admin',
                'email' => 'admin@lichtmoment.de',
                'password' => Hash::make('wasd1234'),
            ]);
        }

        // Check if example project exists
        $project = Project::where('slug', 'sarah-thomas-hochzeit-schloss')->first();
        if (!$project) {
            $project = Project::create([
                'name' => 'Sarah & Thomas – Hochzeit im Schloss',
                'description' => 'Eine traumhafte Hochzeit im historischen Schloss mit atemberaubender Kulisse.',
                'slug' => 'sarah-thomas-hochzeit-schloss',
            ]);

            // Create folders
            $ceremony = \App\Models\Folder::create([
                'project_id' => $project->id,
                'name' => 'Zeremonie',
                'sort_order' => 1,
            ]);

            $reception = \App\Models\Folder::create([
                'project_id' => $project->id,
                'name' => 'Empfang',
                'sort_order' => 2,
            ]);

            $portraits = \App\Models\Folder::create([
                'project_id' => $project->id,
                'name' => 'Portraits',
                'sort_order' => 3,
            ]);

            $details = \App\Models\Folder::create([
                'project_id' => $project->id,
                'name' => 'Details & Deko',
                'sort_order' => 4,
            ]);

            // Create project photos
            $photoData = [
                ['filename' => 'projects/photo_01.jpg', 'name' => 'Erster Tanz', 'folder' => $reception->id],
                ['filename' => 'projects/photo_02.jpg', 'name' => 'Ringtausch', 'folder' => $ceremony->id],
                ['filename' => 'projects/photo_03.jpg', 'name' => 'Brautpaar-Portrait', 'folder' => $portraits->id],
                ['filename' => 'projects/photo_04.jpg', 'name' => 'Hochzeitstorte', 'folder' => $details->id],
                ['filename' => 'projects/photo_05.jpg', 'name' => 'Feier unter Sternen', 'folder' => $reception->id],
                ['filename' => 'projects/photo_06.jpg', 'name' => 'Brautstrauss', 'folder' => $details->id],
                ['filename' => 'projects/photo_07.jpg', 'name' => 'Trauung', 'folder' => $ceremony->id],
                ['filename' => 'projects/photo_08.jpg', 'name' => 'Lovesign', 'folder' => $portraits->id],
                ['filename' => 'projects/photo_09.jpg', 'name' => 'Gäste einfangen', 'folder' => $reception->id],
                ['filename' => 'projects/photo_10.jpg', 'name' => 'Dekoration', 'folder' => $details->id],
            ];

            foreach ($photoData as $i => $data) {
                Photo::create([
                    'project_id' => $project->id,
                    'folder_id' => $data['folder'],
                    'filename' => $data['filename'],
                    'original_name' => $data['name'],
                    'file_size' => rand(1000000, 5000000),
                    'sort_order' => $i + 1,
                ]);
            }
        }

        // Create portfolio placeholder photos (no project_id)
        $existingPortfolio = Photo::whereNull('project_id')->count();
        if ($existingPortfolio === 0) {
            for ($i = 1; $i <= 12; $i++) {
                Photo::create([
                    'project_id' => null,
                    'folder_id' => null,
                    'filename' => "portfolio/hochzeitsfoto_{$i}.jpg",
                    'original_name' => "Portfolio Bild {$i}",
                    'file_size' => 0,
                    'sort_order' => $i,
                ]);
            }
        }
    }
}
