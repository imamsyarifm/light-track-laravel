<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class WilayahService
{
    protected $baseUrl = 'https://wilayah.id/api/';

    /**
     * Mencari Kode Provinsi (YY) dan Kode Kota/Kabupaten (ZZ) berdasarkan nama.
     * @param string $provinsiName
     * @param string $kotaKabupatenName
     * @return array|null [kode_provinsi, kode_kota_kabupaten] atau null jika tidak ditemukan
     */
    public function getKodeWilayah(string $provinsiName, string $kotaKabupatenName): ?array
    {
        try {
            $responseProvinsi = Http::get($this->baseUrl . 'provinces.json');
            if ($responseProvinsi->failed()) {
                return null;
            }
            $provinces = $responseProvinsi->json()['data'];
            $matchedProvinsi = collect($provinces)->first(function ($prov) use ($provinsiName) {
                return strtolower($prov['name']) == strtolower($provinsiName);
            });

            if (!$matchedProvinsi) {
                return null;
            }
            $kodeProvinsi = $matchedProvinsi['code'];

            $responseKota = Http::get($this->baseUrl . 'regencies/' . $kodeProvinsi . '.json');
            if ($responseKota->failed()) {
                return null;
            }
            $regencies = $responseKota->json()['data'];
            
            $matchedKota = collect($regencies)->first(function ($kota) use ($kotaKabupatenName) {
                return strtolower($kota['name']) == strtolower($kotaKabupatenName);
            });

            if (!$matchedKota) {
                return null;
            }
            $kodeKota = substr($matchedKota['code'], -2); 

            return [
                'kode_provinsi' => $kodeProvinsi,
                'kode_kota_kabupaten' => $kodeKota
            ];

        } catch (\Exception $e) {
            logger()->error("Gagal mengambil kode wilayah: " . $e->getMessage());
            return null;
        }
    }
}