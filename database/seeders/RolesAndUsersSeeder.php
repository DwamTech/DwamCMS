<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class RolesAndUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate users table to avoid duplicates or messy data
        Schema::disableForeignKeyConstraints();
        User::truncate();
        Schema::enableForeignKeyConstraints();

        $roles = [
            'admin',
            'editor',
            'author',
            'reviewer',
            'user'
        ];

        // Create one main user for each role with predictable email
        foreach ($roles as $role) {
            User::factory()->create([
                'name' => ucfirst($role) . ' User',
                'email' => strtolower($role) . '@dwam.com',
                'role' => $role,
                'password' => Hash::make('password'), // password
            ]);
        }

        // Create extra random users for each role
        // Admins
        User::factory(4)->create(['role' => 'admin']);
        
        // Editors
        User::factory(10)->create(['role' => 'editor']);
        
        // Authors
        User::factory(15)->create(['role' => 'author']);
        
        // Reviewers
        User::factory(8)->create(['role' => 'reviewer']);
        
        // Normal Users
        User::factory(50)->create(['role' => 'user']);
    }
}
