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

        $songSeeder = new SongSeeder();
        $songSeeder->userId = $user->id;
        $songSeeder->run();

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
