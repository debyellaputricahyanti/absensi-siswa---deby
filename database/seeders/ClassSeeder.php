<?php

namespace Database\Seeders;

use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClassSeeder extends Seeder
{
    public function run(): void
    {
        $classes = User::where('role', 'student')
            ->whereNotNull('kelas')
            ->distinct()
            ->pluck('kelas');

        foreach ($classes as $class) {
            SchoolClass::firstOrCreate(['name' => $class]);
        }
    }
}
