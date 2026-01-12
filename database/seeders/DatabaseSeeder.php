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
        // Create council first
        $psgCouncil = Council::firstOrCreate([
            'code' => 'PSG-UNIWIDE',
        ], [
            'name' => 'Paulinian Student Government',
            'is_active' => true,
            'description' => 'Kupal ka Paulinian Student Government',
        ]);

        // Create user with council_id
        User::firstOrCreate([
            'email' => 'admin@psg.com',
        ], [
            'name' => 'Admin User',
            'id_num' => 'ADM001',
            'contact_number' => '+1234567890',
            'admin' => true,
            'council_id' => $psgCouncil->id,
            'password' => Hash::make('password'),
        ]);
    }
}
