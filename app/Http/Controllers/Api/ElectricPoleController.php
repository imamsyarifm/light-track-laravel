<?php

namespace App\Http\Controllers\Api;

use App\Models\ElectricPole;
use App\Http\Controllers\Controller;
use App\Services\WilayahService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ElectricPoleController extends Controller
{
    protected $wilayahService;

    public function __construct(WilayahService $wilayahService)
    {
        $this->wilayahService = $wilayahService;
    }

    private function validationRules(string $method, ElectricPole $pole = null)
    {
        $rules = [
            'nomor'          => ['required', 'string', 'max:255'],
            'provinsi'       => ['required', 'string', 'max:255'],
            'kota_kabupaten' => ['required', 'string', 'max:255'],
            'kecamatan'      => ['required', 'string', 'max:255'],
            'kelurahan_desa' => ['required', 'string', 'max:255'],
            'alamat'         => ['required', 'string'],
            'koordinat'      => ['nullable', 'string', 'max:255'],
            'foto'           => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ];

        if ($method === 'store') {
            $rules['nomor'][] = 'unique:electric_poles,nomor';
        }

        if ($method === 'update' && $pole) {
            $rules['nomor'][] = 'unique:electric_poles,nomor,' . $pole->id;
        }

        return $rules;
    }

    protected function generateKode(array $data): array
    {
        $kodeWilayah = $this->wilayahService->getKodeWilayah(
            $data['provinsi'],
            $data['kota_kabupaten']
        );

        if (!$kodeWilayah) {
            throw new \Exception("Kode wilayah tidak ditemukan untuk Provinsi/Kota yang diinput.");
        }

        $data['kode_provinsi'] = $kodeWilayah['kode_provinsi'];
        $data['kode_kota_kabupaten'] = $kodeWilayah['kode_kota_kabupaten'];
        $nomorFormatted = str_pad(substr($data['nomor'], -6), 6, '0', STR_PAD_LEFT);
        $data['kode'] = $kodeWilayah['kode_provinsi'] . $kodeWilayah['kode_kota_kabupaten'] . $nomorFormatted;

        return $data;
    }

    /**
     * GET: Mengambil daftar semua tiang listrik.
     */
    public function index()
    {
        $poles = ElectricPole::latest()->get();
        return response()->json([
            'message' => 'Daftar tiang listrik berhasil diambil',
            'data' => $poles
        ]);
    }

    /**
     * POST: Menyimpan data tiang listrik baru.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), $this->validationRules('store'));

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->except('foto');
        try {
            $data = $this->generateKode($data);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('electric_poles', 'public'); 
            $data['foto_url'] = Storage::url($path); 
        }
        
        $pole = ElectricPole::create($data);

        return response()->json([
            'message' => 'Data tiang listrik berhasil ditambahkan',
            'data' => $pole
        ], 201);
    }

    /**
     * GET: Mengambil detail satu tiang listrik.
     */
    public function show(string $id)
    {
        $pole = ElectricPole::find($id);

        if (!$pole) {
            return response()->json([
                'message' => 'Data tiang listrik tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'message' => 'Detail tiang listrik berhasil diambil',
            'data' => $pole
        ]);
    }

    /**
     * PUT/PATCH: Memperbarui data tiang listrik yang ada.
     */
    public function update(Request $request, string $id)
    {
        $pole = ElectricPole::find($id);

        if (!$pole) {
            return response()->json([
                'message' => 'Data tiang listrik tidak ditemukan'
            ], 404);
        }
        
        $validator = Validator::make($request->all(), $this->validationRules('update', $pole));

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $data = $request->except('foto');
        try {
            $data = $this->generateKode($data);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
        
        if ($request->hasFile('foto')) {
            if ($pole->foto_url) {
                $oldPath = str_replace(Storage::url(''), '', $pole->foto_url); 
                Storage::disk('public')->delete($oldPath);
            }
            
            $path = $request->file('foto')->store('electric_poles', 'public');
            $data['foto_url'] = Storage::url($path);
        } else {
            $data['foto_url'] = $pole->foto_url;
        }
        
        $pole->update($data);

        return response()->json([
            'message' => 'Data tiang listrik berhasil diperbarui',
            'data' => $pole
        ]);
    }

    /**
     * DELETE: Menghapus data tiang listrik.
     */
    public function destroy(string $id)
    {
        $pole = ElectricPole::find($id);

        if (!$pole) {
            return response()->json([
                'message' => 'Data tiang listrik tidak ditemukan'
            ], 404);
        }

        $pole->delete();

        return response()->json([
            'message' => 'Data tiang listrik berhasil dihapus'
        ]);
    }
}
