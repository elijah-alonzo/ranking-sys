<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Council;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Council Seeder
        $psgCouncil = Council::firstOrCreate([
            'code' => 'PSG-UNIWIDE',
        ], [
            'name' => 'Paulinian Student Government',
            'is_active' => true,
            'description' => 'Kupal ka Paulinian Student Government',
        ]);

        // User Seeder
        User::firstOrCreate([
            'email' => 'admin@psg.com',
        ], [
            'name' => 'Admin User',
            'contact_number' => '+1234567890',
            'role' => 'admin',
            'is_active' => true,
            'bio' => 'System Administrator',
            'password' => Hash::make('password'),
        ]);

        User::firstOrCreate([
            'email' => 'adviser@psg.com',
        ], [
            'name' => 'Adviser User',
            'contact_number' => '+1234567891',
            'role' => 'adviser',
            'is_active' => true,
            'bio' => 'Faculty Adviser',
            'password' => Hash::make('password'),
        ]);

        User::firstOrCreate([
            'email' => 'student@psg.com',
        ], [
            'name' => 'Student User',
            'contact_number' => '+1234567892',
            'role' => 'student',
            'is_active' => true,
            'bio' => 'Student Member',
            'password' => Hash::make('password'),
        ]);
    }
}
