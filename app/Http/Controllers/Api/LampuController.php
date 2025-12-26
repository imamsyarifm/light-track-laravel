<?php

namespace App\Http\Controllers\Api;

use App\Models\Lampu;
use App\Models\ElectricPole;
use App\Http\Controllers\Controller;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class LampuController extends Controller
{
    protected $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }
    
    private function validationRules(string $method, Lampu $lampu = null)
    {
        $rules = [
            'electric_pole_id' => ['required', 'exists:electric_poles,id'],
            'nomor'            => ['required', 'string', 'max:255'],
            'koordinat'        => ['nullable', 'string', 'max:255'],
            'foto'             => ['nullable', 'array', 'max:4'],
            'foto.*'           => ['nullable'],
        ];

        // Notes: temporary not using unique rules
        // if ($method === 'store') {
        //     $rules['nomor'][] = 'unique:lampus,nomor';
        // }

        // if ($method === 'update' && $lampu) {
        //     $rules['nomor'][] = 'unique:lampus,nomor,' . $lampu->id;
        // }

        return $rules;
    }

    public function index()
    {
        $lampus = Lampu::with('electricPole')->latest()->get();
        return response()->json([
            'message' => 'Daftar lampu berhasil diambil',
            'data' => $lampus
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
        $data['foto_urls'] = $this->fileUploadService->handleMultipleUpload($request, 'foto', 'lampus');
        
        $lampu = Lampu::create($data);

        return response()->json(['message' => 'Data lampu berhasil ditambahkan', 'data' => $lampu], 201);
    }

    public function show(string $id)
    {
        $lampu = Lampu::with('electricPole')->find($id);

        if (!$lampu) {
            return response()->json(['message' => 'Data lampu tidak ditemukan'], 404);
        }

        return response()->json(['message' => 'Detail lampu berhasil diambil', 'data' => $lampu]);
    }

    public function update(Request $request, string $id)
    {
        $lampu = Lampu::find($id);

        if (!$lampu) {
            return response()->json(['message' => 'Data lampu tidak ditemukan'], 404);
        }
        
        $validator = Validator::make($request->all(), $this->validationRules('update', $lampu));

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
                $newUploadedPaths = $this->fileUploadService->handleMultipleUpload($request, 'foto', 'lampus');
                $finalPaths = array_merge($finalPaths, $newUploadedPaths);
            }

            $oldPathsInDb = json_decode($lampu->getRawOriginal('foto_urls'), true) ?? [];
            $deletedPaths = array_diff($oldPathsInDb, $finalPaths);

            foreach ($deletedPaths as $pathToDelete) {
                if (Storage::disk('public')->exists($pathToDelete)) {
                    Storage::disk('public')->delete($pathToDelete);
                }
            }

            $poleId = $request->has('electric_pole_id') ? $request->electric_pole_id : $lampu->electric_pole_id;
            $pole = ElectricPole::findOrFail($poleId);

            $data = $request->except('foto');
            $data['foto_urls'] = $finalPaths;
            $data['kode'] = $pole->kode . '-' . ($data['nomor'] ?? 
            
            $lampu->nomor);
            $lampu->update($data);

            return response()->json([
                'message' => 'Data lampu berhasil diperbarui',
                'data' => $lampu
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function destroy(string $id)
    {
        $lampu = Lampu::find($id);

        if (!$lampu) {
            return response()->json(['message' => 'Data lampu tidak ditemukan'], 404);
        }

        $this->fileUploadService->deleteMultipleFiles($lampu->foto_urls);
        $lampu->delete();

        return response()->json(['message' => 'Data lampu berhasil dihapus']);
    }
}