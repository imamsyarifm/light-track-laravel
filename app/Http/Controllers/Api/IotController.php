<?php

namespace App\Http\Controllers\Api;

use App\Models\Iot;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class IotController extends Controller
{
    private function validationRules(string $method, Iot $iot = null)
    {
        $rules = [
            'electric_pole_id' => ['required', 'exists:electric_poles,id'],
            'nomor'            => ['required', 'string', 'max:255'],
            'koordinat'        => ['nullable', 'string', 'max:255'],
        ];

        if ($method === 'store') {
            $rules['nomor'][] = 'unique:iots,nomor';
        }

        if ($method === 'update' && $iot) {
            $rules['nomor'][] = 'unique:iots,nomor,' . $iot->id;
        }

        return $rules;
    }

    public function index()
    {
        $iots = Iot::with('electricPole')->latest()->get();
        return response()->json([
            'message' => 'Daftar IoT berhasil diambil',
            'data' => $iots
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), $this->validationRules('store'));

        if ($validator->fails()) {
            return response()->json(['message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }
        
        $iot = Iot::create($request->all());

        return response()->json(['message' => 'Data IoT berhasil ditambahkan', 'data' => $iot], 201);
    }

    public function show(string $id)
    {
        $iot = Iot::with('electricPole')->find($id);

        if (!$iot) {
            return response()->json(['message' => 'Data IoT tidak ditemukan'], 404);
        }

        return response()->json(['message' => 'Detail IoT berhasil diambil', 'data' => $iot]);
    }

    public function update(Request $request, string $id)
    {
        $iot = Iot::find($id);

        if (!$iot) {
            return response()->json(['message' => 'Data IoT tidak ditemukan'], 404);
        }
        
        $validator = Validator::make($request->all(), $this->validationRules('update', $iot));

        if ($validator->fails()) {
            return response()->json(['message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }
        
        $iot->update($request->all());

        return response()->json(['message' => 'Data IoT berhasil diperbarui', 'data' => $iot]);
    }

    public function destroy(string $id)
    {
        $iot = Iot::find($id);

        if (!$iot) {
            return response()->json(['message' => 'Data IoT tidak ditemukan'], 404);
        }

        $iot->delete();

        return response()->json(['message' => 'Data IoT berhasil dihapus']);
    }
}
