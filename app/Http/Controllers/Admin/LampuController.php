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
        return view('admin.lampus.create', compact('poles'));
    }

    public function index()
    {
        $search = request('search');
        
        $lampus = Lampu::with('electricPole')
            ->latest()
            ->when($search, function ($query, $search) {
                return $query->where('nomor', 'like', "%{$search}%")
                             ->orWhereHas('electricPole', function ($q) use ($search) {
                                 $q->where('kode', 'like', "%{$search}%");
                             });
            })
            ->paginate(15);
            
        return view('admin.lampus.index', compact('lampus'));
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
        } else {
            $data['foto_url'] = null;
        }

        Lampu::create($data);
        return redirect()->route('admin.lampus.index')->with('success', "Lampu '{$data['kode']}' berhasil ditambahkan.");
    }

    public function edit(Lampu $lampu)
    {
        $poles = ElectricPole::select('id', 'nomor', 'kode')->get();
        return view('admin.lampus.edit', compact('lampu', 'poles'));
    }
    
    public function update(Request $request, Lampu $lampu)
    {
        $request->validate([
            'electric_pole_id'  => 'required|exists:electric_poles,id',
            'nomor'             => 'required|string|unique:lampus,nomor,' . $lampu->id, 
            // 'koordinat'         => 'nullable|string',
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
        return redirect()->route('admin.lampus.index')->with('success', "Lampu '{$lampu->kode}' berhasil diperbarui.");
    }

    public function destroy(Lampu $lampu)
    {
        if ($lampu->foto_url) {
            $oldPath = str_replace(Storage::url(''), '', $lampu->foto_url); 
            Storage::disk('public')->delete($oldPath);
        }
        $lampu->delete();
        
        return redirect()->route('admin.lampus.index')->with('success', "Lampu '{$lampu->kode}' berhasil dihapus.");
    }
}
