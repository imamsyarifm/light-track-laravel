<?php

namespace App\Http\Controllers\Admin;

use App\Models\ElectricPole;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Services\WilayahService;

class ElectricPoleController extends Controller
{
    protected $wilayahService;

    public function __construct(WilayahService $wilayahService)
    {
        $this->wilayahService = $wilayahService;
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

    public function index()
    {
        $poles = ElectricPole::latest()->get();
        return "Electric Poles Index (Total: {$poles->count()})";
    }

    public function create()
    {
        return "Electric Poles Create Form (Backend Only)";
    }

    public function store(Request $request)
    {
        $request->validate([
            'nomor' => 'required|string|unique:electric_poles,nomor',
            'provinsi' => 'required|string',
            'kota_kabupaten' => 'required|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        
        try {
            $data = $this->generateKode($request->except('foto'));
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('electric_poles', 'public'); 
            $data['foto_url'] = Storage::url($path); 
        }

        ElectricPole::create($data);

        return "SUCCESS: Tiang Listrik '{$data['kode']}' berhasil ditambahkan. Redirecting...";
    }

    public function edit(ElectricPole $pole)
    {
        return "Electric Poles Edit Form for ID: {$pole->id}";
    }
    
    public function update(Request $request, ElectricPole $pole)
    {
        $request->validate([
            'nomor' => 'required|string|unique:electric_poles,nomor,'.$pole->id,
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            $data = $this->generateKode($request->except('foto'));
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
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

        return "SUCCESS: Tiang Listrik ID {$pole->id} diperbarui. Redirecting...";
    }
    
    public function destroy(ElectricPole $pole)
    {
        if ($pole->foto_url) {
            $oldPath = str_replace(Storage::url(''), '', $pole->foto_url); 
            Storage::disk('public')->delete($oldPath);
        }
        $pole->delete();
        
        return "SUCCESS: Tiang Listrik ID {$pole->id} dihapus. Redirecting...";
    }
}