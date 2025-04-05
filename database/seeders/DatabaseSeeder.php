<?php

namespace Database\Seeders;

use App\Models\Request;
use App\Models\Songs;
use App\Models\User;
use App\UserRole;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@example.com',
            'role' => UserRole::ADMIN->value
        ]);

       $songs = [
            [
                'title' => 'O Mineiro e o Italiano',
                'views' => 5200000,
                'youtube_id' => 's9kVG2ZaTS4',
                'thumb' => 'https://img.youtube.com/vi/s9kVG2ZaTS4/hqdefault.jpg'
            ],
            [
                'title' => 'Pagode em Brasília',
                'views' => 5000000,
                'youtube_id' => 'lpGGNA6_920',
                'thumb' => 'https://img.youtube.com/vi/lpGGNA6_920/hqdefault.jpg'
            ],
            [
                'title' => 'Rio de Lágrimas',
                'views' => 153000,
                'youtube_id' => 'FxXXvPL3JIg',
                'thumb' => 'https://img.youtube.com/vi/FxXXvPL3JIg/hqdefault.jpg'
            ],
            [
                'title' => 'Tristeza do Jeca',
                'views' => 154000,
                'youtube_id' => 'tRQ2PWlCcZk',
                'thumb' => 'https://img.youtube.com/vi/tRQ2PWlCcZk/hqdefault.jpg'
            ],
            [
                'title' => 'Terra roxa',
                'views' => 3300000,
                'youtube_id' => '4Nb89GFu2g4',
                'thumb' => 'https://img.youtube.com/vi/4Nb89GFu2g4/hqdefault.jpg'
            ]
        ];

        foreach ($songs as $song) {
            Songs::create(array_merge($song, ['user_id' => $user->id]));
        }

        Request::create([
            'user_id' => $user->id,
            'link' => 'https://www.youtube.com/watch?v=VrvWrkbXfXE&list=OLAK5uy_kffAkF2oQoGwZEprsKofDxhXYAPTbg8eA'
        ]);

        Request::create([
            'user_id' => $user->id,
            'link' => 'https://www.youtube.com/watch?si=X8GG6MXzrVo2QP54&v=Fv3XB-RmaVM&feature=youtu.be'
        ]);
    }
}
