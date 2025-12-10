<?php

namespace App\Http\Controllers\Api;

use App\Models\Lampu;
use App\Models\ElectricPole;
use App\Http\Controllers\Controller;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
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
            'foto.*'           => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ];

        if ($method === 'store') {
            $rules['nomor'][] = 'unique:lampus,nomor';
        }

        if ($method === 'update' && $lampu) {
            $rules['nomor'][] = 'unique:lampus,nomor,' . $lampu->id;
        }

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

        $poleId = $request->has('electric_pole_id') ? $request->electric_pole_id : $lampu->electric_pole_id;
        $pole = ElectricPole::findOrFail($poleId);

        $data = $request->except('foto');
        $data['kode'] = $pole->kode . '-' . $data['nomor'];
        $data['foto_urls'] = $this->fileUploadService->updateMultipleUpload(
            $request, 
            $lampu, 
            'foto', 
            'foto_urls',
            'lampus' 
        );
        
        $lampu->update($data);

        return response()->json(['message' => 'Data lampu berhasil diperbarui', 'data' => $lampu]);
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