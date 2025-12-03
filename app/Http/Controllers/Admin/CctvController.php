<?php

namespace App\Http\Controllers\Admin;

use App\Models\Cctv;
use App\Models\ElectricPole;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class CctvController extends Controller
{
    public function create()
    {
        $poles = ElectricPole::select('id', 'nomor', 'kode')->get();
        return "CCTV Create Form. Tiang Listrik tersedia: {$poles->count()}";
    }

    public function index()
    {
        $cctv = Cctv::latest()->get();
        return "CCTV Index (Total: {$cctv->count()})";
    }

    public function store(Request $request)
    {
        $request->validate([
            'electric_pole_id' => 'required|exists:electric_poles,id',
            'nomor'            => 'required|string|unique:cctvs,nomor',
            'koordinat'        => 'nullable|string',
            'foto'             => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        
        $pole = ElectricPole::findOrFail($request->electric_pole_id);

        $data = $request->except('foto');
        $data['kode'] = $pole->kode . '-' . $data['nomor']; 

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('cctvs', 'public'); 
            $data['foto_url'] = Storage::url($path); 
        }

        Cctv::create($data);
        return "SUCCESS: CCTV '{$data['kode']}' berhasil ditambahkan. Redirecting...";
    }

    public function edit(Cctv $cctv)
    {
        return "CCTV Edit Form for ID: {$cctv->id}";
    }
    
    public function update(Request $request, Cctv $cctv)
    {
        $request->validate([
            'electric_pole_id'  => 'required|exists:electric_poles,id',
            'nomor'             => 'required|string|unique:cctvs,nomor,' . $cctv->id, 
            'foto'              => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $poleId = $request->has('electric_pole_id') ? $request->electric_pole_id : $cctv->electric_pole_id;
        $pole = ElectricPole::findOrFail($poleId);

        $data = $request->except('foto');
        $data['kode'] = $pole->kode . '-' . $data['nomor'];

        if ($request->hasFile('foto')) {
            if ($cctv->foto_url) { 
                $oldPath = str_replace(Storage::url(''), '', $cctv->foto_url); 
                Storage::disk('public')->delete($oldPath);
            }
            
            $path = $request->file('foto')->store('cctvs', 'public'); 
            $data['foto_url'] = Storage::url($path);
        } else {
            $data['foto_url'] = $cctv->foto_url;
        }

        $cctv->update($data);
        return "SUCCESS: CCTV ID {$cctv->id} diperbarui. Redirecting...";
    }

    public function destroy(Cctv $cctv)
    {
        if ($cctv->foto_url) {
            $oldPath = str_replace(Storage::url(''), '', $cctv->foto_url); 
            Storage::disk('public')->delete($oldPath);
        }
        $cctv->delete();
        
        return "SUCCESS: CCTV ID {$cctv->id} dihapus. Redirecting...";
    }
}
