<?php

namespace Database\Seeders;

use App\Models\Diagnose;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DiagnoseSeeder extends Seeder
{
    public function run(): void
    {
        $diagnoses = [
            ['name' => 'Ringan'],
            ['name' => 'Berat'],
            ['name' => 'Kritis']
        ];

        foreach ($diagnoses as $diagnose) {
            Diagnose::create($diagnose);
        }
    }
}
