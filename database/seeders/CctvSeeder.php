<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ElectricPole;
use App\Models\Cctv;

class CctvSeeder extends Seeder
{
    public function run(): void
    {
        $pole2 = ElectricPole::where('nomor', 'PLN-JKT-002')->first();
        $pole4 = ElectricPole::where('nomor', 'PLN-SBY-004')->first();
        
        if (!$pole2 || !$pole4) {
            echo "Pastikan ElectricPoleSeeder sudah dijalankan.\n";
            return;
        }

        $cctvs = [
            [
                'nomor' => 'CCTV-UTAMA-002',
                'electric_pole_id' => $pole2->id,
                'koordinat' => '-6.2208, 106.8020',
            ],
            [
                'nomor' => 'CCTV-PERUM-004',
                'electric_pole_id' => $pole4->id,
                'koordinat' => '-7.2652, 112.7533',
            ],
        ];

        foreach ($cctvs as $cctv) {
            if (!Cctv::where('nomor', $cctv['nomor'])->exists()) {
                Cctv::create($cctv);
            }
        }
    }
}