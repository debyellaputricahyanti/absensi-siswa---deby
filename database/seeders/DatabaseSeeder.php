<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\User;
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
        // Admin
        User::create([
            'name' => 'Admin',
            'email' => 'admin@school.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Student (Budi)
        User::create([
            'name' => 'Budi Santoso',
            'nis' => '20260001',
            'kelas' => 'X-1',
            'email' => 'budi@school.com',
            'password' => Hash::make('password'),
            'role' => 'student',
        ]);

        // Settings (Geofence)
        Setting::create(['key' => 'latitude', 'value' => '-6.175392']);
        Setting::create(['key' => 'longitude', 'value' => '106.827153']);
        Setting::create(['key' => 'radius', 'value' => '100']); // meters
    }
}
