<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ElectricPole;
use App\Models\Lampu;

class LampuSeeder extends Seeder
{
    public function run(): void
    {
        $pole1 = ElectricPole::where('nomor', 'PLN-BDG-001')->first();
        $pole2 = ElectricPole::where('nomor', 'PLN-JKT-002')->first();
        
        if (!$pole1 || !$pole2) {
            echo "Pastikan ElectricPoleSeeder sudah dijalankan.\n";
            return;
        }

        $lampus = [
            [
                'nomor' => 'LMP-001-BDG',
                'electric_pole_id' => $pole1->id,
                'koordinat' => '-6.8906, 107.6106',
            ],
            [
                'nomor' => 'LMP-002-JKT',
                'electric_pole_id' => $pole2->id,
                'koordinat' => '-6.2208, 106.8020',
            ],
        ];

        foreach ($lampus as $lampu) {
            if (!Lampu::where('nomor', $lampu['nomor'])->exists()) {
                Lampu::create($lampu);
            }
        }
    }
}