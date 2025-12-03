<?php

namespace App\Http\Controllers\Admin;

use App\Models\Iot;
use App\Models\ElectricPole;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class IotController extends Controller
{
    public function create()
    {
        $poles = ElectricPole::select('id', 'nomor', 'kode')->get();
        return "IoT Create Form. Tiang Listrik tersedia: {$poles->count()}";
    }

    public function index()
    {
        $iot = Iot::latest()->get();
        return "IoT Index (Total: {$iot->count()})";
    }

    public function store(Request $request)
    {
        $request->validate([
            'electric_pole_id' => 'required|exists:electric_poles,id',
            'nomor'            => 'required|string|unique:iots,nomor',
            'koordinat'        => 'nullable|string',
            'foto'             => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        
        $pole = ElectricPole::findOrFail($request->electric_pole_id);

        $data = $request->except('foto');
        $data['kode'] = $pole->kode . '-' . $data['nomor']; 

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('iots', 'public'); 
            $data['foto_url'] = Storage::url($path); 
        }

        Iot::create($data);
        return "SUCCESS: IoT '{$data['kode']}' berhasil ditambahkan. Redirecting...";
    }

    public function edit(Iot $iot)
    {
        return "IoT Edit Form for ID: {$iot->id}";
    }

    public function update(Request $request, Iot $iot)
    {
        $request->validate([
            'electric_pole_id'  => 'required|exists:electric_poles,id',
            'nomor'             => 'required|string|unique:iots,nomor,' . $iot->id, 
            'foto'              => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $poleId = $request->has('electric_pole_id') ? $request->electric_pole_id : $iot->electric_pole_id;
        $pole = ElectricPole::findOrFail($poleId);

        $data = $request->except('foto');
        $data['kode'] = $pole->kode . '-' . $data['nomor'];

        if ($request->hasFile('foto')) {
            if ($iot->foto_url) { 
                $oldPath = str_replace(Storage::url(''), '', $iot->foto_url); 
                Storage::disk('public')->delete($oldPath);
            }
            
            $path = $request->file('foto')->store('iots', 'public'); 
            $data['foto_url'] = Storage::url($path);
        } else {
            $data['foto_url'] = $iot->foto_url;
        }

        $iot->update($data);
        return "SUCCESS: IoT ID {$iot->id} diperbarui. Redirecting...";
    }

    public function destroy(Iot $iot)
    {
        if ($iot->foto_url) {
            $oldPath = str_replace(Storage::url(''), '', $iot->foto_url); 
            Storage::disk('public')->delete($oldPath);
        }
        $iot->delete();
        
        return "SUCCESS: IoT ID {$iot->id} dihapus. Redirecting...";
    }
}
