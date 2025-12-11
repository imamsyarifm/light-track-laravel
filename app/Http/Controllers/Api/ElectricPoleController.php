<?php

namespace App\Http\Controllers\Api;

use App\Models\ElectricPole;
use App\Http\Controllers\Controller;
use App\Services\FileUploadService;
use App\Services\WilayahService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ElectricPoleController extends Controller
{
    protected $fileUploadService;
    protected $wilayahService;

    public function __construct(FileUploadService $fileUploadService, WilayahService $wilayahService)
    {
        $this->fileUploadService = $fileUploadService;
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
            'foto'           => ['nullable', 'array', 'max:4'],
            'foto.*'         => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
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
    public function index(Request $request)
    {
        $keyword    = $request->keyword;
        $sortBy     = $request->sort_by ?? 'created_at';
        $sortOrder  = $request->sort_order ?? 'desc';

        $query = ElectricPole::query();

        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('nomor', 'like', '%' . $keyword . '%')
                ->orWhere('kode', 'like', '%' . $keyword . '%');
            });
        }

        if (in_array($sortOrder, ['asc', 'desc'])) {
            $query->orderBy($sortBy, $sortOrder);
        }

        $poles = $query->get();

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

        $data['foto_urls'] = $this->fileUploadService->handleMultipleUpload($request, 'foto', 'electric_poles');
        
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
        
        $data['foto_urls'] = $this->fileUploadService->updateMultipleUpload(
            $request, 
            $pole, 
            'foto', 
            'foto_urls',
            'electric_poles' 
        );
        
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
        
        $this->fileUploadService->deleteMultipleFiles($pole->foto_urls);
        $pole->delete();

        return response()->json([
            'message' => 'Data tiang listrik berhasil dihapus'
        ]);
    }

    public function showWithRelations(string $id)
    {
        $pole = ElectricPole::with(['lampus', 'iots', 'cctvs'])
                            ->find($id);

        if (!$pole) {
            return response()->json([
                'message' => 'Tiang Listrik tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'message' => 'Detail Tiang Listrik beserta relasi berhasil diambil',
            'data' => $pole
        ]);
    }
}
