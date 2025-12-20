<?php

namespace App\Http\Controllers\Api;

use App\Models\Cctv;
use App\Models\ElectricPole;
use App\Http\Controllers\Controller;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CctvController extends Controller
{
    protected $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    private function validationRules(string $method, Cctv $cctv = null)
    {
        $rules = [
            'electric_pole_id' => ['required', 'exists:electric_poles,id'],
            'nomor'            => ['required', 'string', 'max:255'],
            'koordinat'        => ['nullable', 'string', 'max:255'],
            'foto'             => ['nullable', 'array', 'max:4'],
            'foto.*'           => ['nullable'],
        ];

        if ($method === 'store') {
            $rules['nomor'][] = 'unique:cctvs,nomor';
        }

        if ($method === 'update' && $cctv) {
            $rules['nomor'][] = 'unique:cctvs,nomor,' . $cctv->id;
        }

        return $rules;
    }

    public function index()
    {
        $cctvs = Cctv::with('electricPole')->latest()->get();
        return response()->json([
            'message' => 'Daftar CCTV berhasil diambil',
            'data' => $cctvs
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), $this->validationRules('store'));

        if ($validator->fails()) {
            return response()->json(['message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        $pole = ElectricPole::findOrFail($request->electric_pole_id);

        $data = $request->except('foto');
        $data['kode'] = $pole->kode . '-' . $data['nomor'];
        $data['foto_urls'] = $this->fileUploadService->handleMultipleUpload($request, 'foto', 'cctvs');
        
        $cctv = Cctv::create($data);

        return response()->json(['message' => 'Data CCTV berhasil ditambahkan', 'data' => $cctv], 201);
    }

    public function show(string $id)
    {
        $cctv = Cctv::with('electricPole')->find($id);

        if (!$cctv) {
            return response()->json(['message' => 'Data CCTV tidak ditemukan'], 404);
        }

        return response()->json(['message' => 'Detail CCTV berhasil diambil', 'data' => $cctv]);
    }

    public function update(Request $request, string $id)
    {
        $cctv = Cctv::find($id);

        if (!$cctv) {
            return response()->json(['message' => 'Data CCTV tidak ditemukan'], 404);
        }
        
        $validator = Validator::make($request->all(), $this->validationRules('update', $cctv));

        if ($validator->fails()) {
            return response()->json(['message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        try {
            $finalPaths = [];
            $inputPhotos = $request->input('foto', []);

            if (is_array($inputPhotos)) {
                foreach ($inputPhotos as $item) {
                    if (is_string($item) && !empty($item)) {
                        $path = Str::after($item, 'storage/');
                        $finalPaths[] = $path;
                    }
                }
            }

            if ($request->hasFile('foto')) {
                $newUploadedPaths = $this->fileUploadService->handleMultipleUpload($request, 'foto', 'cctvs');
                $finalPaths = array_merge($finalPaths, $newUploadedPaths);
            }

            $oldPathsInDb = json_decode($cctv->getRawOriginal('foto_urls'), true) ?? [];
            $deletedPaths = array_diff($oldPathsInDb, $finalPaths);

            foreach ($deletedPaths as $pathToDelete) {
                if (Storage::disk('public')->exists($pathToDelete)) {
                    Storage::disk('public')->delete($pathToDelete);
                }
            }

            $poleId = $request->has('electric_pole_id') ? $request->electric_pole_id : $cctv->electric_pole_id;
            $pole = ElectricPole::findOrFail($poleId);

            $data = $request->except('foto');
            $data['foto_urls'] = $finalPaths;
            $data['kode'] = $pole->kode . '-' . ($data['nomor'] ?? 
            
            $cctv->nomor);
            $cctv->update($data);

            return response()->json([
                'message' => 'Data CCTV berhasil diperbarui',
                'data' => $cctv
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function destroy(string $id)
    {
        $cctv = Cctv::find($id);

        if (!$cctv) {
            return response()->json(['message' => 'Data CCTV tidak ditemukan'], 404);
        }

        $this->fileUploadService->deleteMultipleFiles($cctv->foto_urls);
        $cctv->delete();

        return response()->json(['message' => 'Data CCTV berhasil dihapus']);
    }
}
