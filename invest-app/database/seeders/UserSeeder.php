<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create user if it doesn't exist
        $user = User::firstOrCreate(
            ['email' => 'admin@email.com'],
            [
                'name' => 'admin',
                'password' => Hash::make('password1234'),
                'assigned_sector' => null,
            ]
        );

        // Assign role (requires spatie/laravel-permission)
        if (! $user->hasRole('admin')) {
            $user->assignRole('admin');
        }
    }
}
