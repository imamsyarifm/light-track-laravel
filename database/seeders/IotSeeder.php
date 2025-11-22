<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ElectricPole;
use App\Models\Iot;

class IotSeeder extends Seeder
{
    public function run(): void
    {
        $pole1 = ElectricPole::where('nomor', 'PLN-BDG-001')->first();
        $pole3 = ElectricPole::where('nomor', 'PLN-BDG-003')->first();
        
        if (!$pole1 || !$pole3) {
            echo "Pastikan ElectricPoleSeeder sudah dijalankan.\n";
            return;
        }

        $iots = [
            [
                'nomor' => 'IOT-SENSE-001',
                'electric_pole_id' => $pole1->id,
                'koordinat' => '-6.8906, 107.6106',
            ],
            [
                'nomor' => 'IOT-NODE-003',
                'electric_pole_id' => $pole3->id,
                'koordinat' => '-6.9187, 107.6766',
            ],
        ];

        foreach ($iots as $iot) {
            if (!Iot::where('nomor', $iot['nomor'])->exists()) {
                Iot::create($iot);
            }
        }
    }
}