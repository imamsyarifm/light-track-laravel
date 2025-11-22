<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ElectricPole;

class ElectricPoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $poles = [
            [
                'nomor' => 'PLN-BDG-001',
                'provinsi' => 'Jawa Barat',
                'kota_kabupaten' => 'Kota Bandung',
                'kecamatan' => 'Coblong',
                'kelurahan_desa' => 'Dago',
                'alamat' => 'Depan gerbang ITB',
                'koordinat' => '-6.8906, 107.6106',
            ],
            [
                'nomor' => 'PLN-JKT-002',
                'provinsi' => 'DKI Jakarta',
                'kota_kabupaten' => 'Jakarta Selatan',
                'kecamatan' => 'Kebayoran Baru',
                'kelurahan_desa' => 'Senayan',
                'alamat' => 'Dekat stadion utama',
                'koordinat' => '-6.2208, 106.8020',
            ],
            [
                'nomor' => 'PLN-BDG-003',
                'provinsi' => 'Jawa Barat',
                'kota_kabupaten' => 'Kota Bandung',
                'kecamatan' => 'Arcamanik',
                'kelurahan_desa' => 'Jatihandap',
                'alamat' => 'Jalan pacuan kuda',
                'koordinat' => '-6.9187, 107.6766',
            ],
            [
                'nomor' => 'PLN-SBY-004',
                'provinsi' => 'Jawa Timur',
                'kota_kabupaten' => 'Kota Surabaya',
                'kecamatan' => 'Genteng',
                'kelurahan_desa' => 'Embong Kaliasin',
                'alamat' => 'Dekat Balai Kota',
                'koordinat' => '-7.2652, 112.7533',
            ],
        ];

        foreach ($poles as $pole) {
            if (!ElectricPole::where('nomor', $pole['nomor'])->exists()) {
                ElectricPole::create($pole);
            }
        }
    }
}