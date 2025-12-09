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
        $search = request('search');
        
        $poles = ElectricPole::latest()
            ->when($search, function ($query, $search) {
                return $query->where('nomor', 'like', "%{$search}%")
                             ->orWhere('provinsi', 'like', "%{$search}%")
                             ->orWhere('kota_kabupaten', 'like', "%{$search}%");
            })
            ->paginate(15);

        return view('admin.poles.index', compact('poles'));
    }

    public function create()
    {
        return view('admin.poles.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nomor' => 'required|string|unique:electric_poles,nomor',
            'provinsi' => 'required|string',
            'kota_kabupaten' => 'required|string',
            // 'kecamatan' => 'required|string',
            // 'kelurahan_desa' => 'required|string',
            // 'alamat' => 'required|string',
            // 'koordinat' => 'nullable|string',
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
        }else {
            $data['foto_url'] = null;
        }

        ElectricPole::create($data);

        return redirect('/tiang-lampu')->with('success', "Tiang Listrik '{$data['kode']}' berhasil ditambahkan.");
    }

    public function edit(ElectricPole $pole)
    {
        return view('admin.poles.edit', compact('pole'));
    }
    
    public function update(Request $request, ElectricPole $pole)
    {
        $request->validate([
            'nomor' => 'required|string|unique:electric_poles,nomor,'.$pole->id,
            // 'provinsi' => 'required|string',
            // 'kota_kabupaten' => 'required|string',
            // 'kecamatan' => 'required|string',
            // 'kelurahan_desa' => 'required|string',
            // 'alamat' => 'required|string',
            // 'koordinat' => 'nullable|string',
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

        return redirect()->route('admin.poles.index')->with('success', "Tiang Listrik '{$pole->kode}' berhasil diperbarui.");
    }
    
    public function destroy(ElectricPole $pole)
    {
        if ($pole->foto_url) {
            $oldPath = str_replace(Storage::url(''), '', $pole->foto_url); 
            Storage::disk('public')->delete($oldPath);
        }
        $pole->delete();
        
        return redirect()->route('admin.poles.index')->with('success', "Tiang Listrik '{$pole->kode}' berhasil dihapus.");
    }
}