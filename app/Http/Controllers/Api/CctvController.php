<?php

namespace App\Http\Controllers\Api;

use App\Models\Cctv;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CctvController extends Controller
{
    private function validationRules(string $method, Cctv $cctv = null)
    {
        $rules = [
            'electric_pole_id' => ['required', 'exists:electric_poles,id'],
            'nomor'            => ['required', 'string', 'max:255'],
            'koordinat'        => ['nullable', 'string', 'max:255'],
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
        
        $cctv = Cctv::create($request->all());

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
        
        $cctv->update($request->all());

        return response()->json(['message' => 'Data CCTV berhasil diperbarui', 'data' => $cctv]);
    }

    public function destroy(string $id)
    {
        $cctv = Cctv::find($id);

        if (!$cctv) {
            return response()->json(['message' => 'Data CCTV tidak ditemukan'], 404);
        }

        $cctv->delete();

        return response()->json(['message' => 'Data CCTV berhasil dihapus']);
    }
}
