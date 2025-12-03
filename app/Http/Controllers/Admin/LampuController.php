<?php

namespace App\Http\Controllers\Admin;

use App\Models\Lampu;
use App\Models\ElectricPole;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class LampuController extends Controller
{
    public function create()
    {
        $poles = ElectricPole::select('id', 'nomor', 'kode')->get();
        return "Lampu Create Form. Tiang Listrik tersedia: {$poles->count()}";
    }

    public function index()
    {
        $lampu = Lampu::latest()->get();
        return "Lampu Index (Total: {$lampu->count()})";
    }

    public function store(Request $request)
    {
        $request->validate([
            'electric_pole_id' => 'required|exists:electric_poles,id',
            'nomor'            => 'required|string|unique:lampus,nomor',
            'koordinat'        => 'nullable|string',
            'foto'             => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        
        $pole = ElectricPole::findOrFail($request->electric_pole_id);

        $data = $request->except('foto');
        $data['kode'] = $pole->kode . '-' . $data['nomor']; 

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('lampus', 'public'); 
            $data['foto_url'] = Storage::url($path); 
        }

        Lampu::create($data);
        return "SUCCESS: Lampu '{$data['kode']}' berhasil ditambahkan. Redirecting...";
    }

    public function edit(Lampu $lampu)
    {
        return "Lampu Edit Form for ID: {$lampu->id}";
    }
    
    public function update(Request $request, Lampu $lampu)
    {
        $request->validate([
            'electric_pole_id'  => 'required|exists:electric_poles,id',
            'nomor'             => 'required|string|unique:lampus,nomor,' . $lampu->id, 
            'foto'              => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $poleId = $request->has('electric_pole_id') ? $request->electric_pole_id : $lampu->electric_pole_id;
        $pole = ElectricPole::findOrFail($poleId);

        $data = $request->except('foto');
        $data['kode'] = $pole->kode . '-' . $data['nomor'];

        if ($request->hasFile('foto')) {
            if ($lampu->foto_url) { 
                $oldPath = str_replace(Storage::url(''), '', $lampu->foto_url); 
                Storage::disk('public')->delete($oldPath);
            }
            
            $path = $request->file('foto')->store('lampus', 'public'); 
            $data['foto_url'] = Storage::url($path);
        } else {
            $data['foto_url'] = $lampu->foto_url;
        }

        $lampu->update($data);
        return "SUCCESS: Lampu ID {$lampu->id} diperbarui. Redirecting...";
    }

    public function destroy(Lampu $lampu)
    {
        if ($lampu->foto_url) {
            $oldPath = str_replace(Storage::url(''), '', $lampu->foto_url); 
            Storage::disk('public')->delete($oldPath);
        }
        $lampu->delete();
        
        return "SUCCESS: Lampu ID {$lampu->id} dihapus. Redirecting...";
    }
}
