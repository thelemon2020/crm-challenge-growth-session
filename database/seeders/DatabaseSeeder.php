<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolePermissionSeeder::class);

        $user1 = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);
        $user2 = User::factory()->create([
            'name' => 'Test User2',
            'email' => 'test2@example.com',
            'password' => Hash::make('password'),
        ]);

        $user1->assignRole('admin');
        $user2->assignRole('user');

        Project::factory()->count(10)->create([
            'user_id' => $user1->id,
        ]);
        Project::factory()->count(5)->create([
            'user_id' => $user2->id,
        ]);
    }
}
