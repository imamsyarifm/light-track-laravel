<?php

namespace App\Http\Controllers\Admin;

use App\Models\Lampu;
use App\Models\ElectricPole;
use App\Http\Controllers\Controller;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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
        $search = request('search');
        
        $lampus = Lampu::with('electricPole')
            ->latest()
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nomor', 'like', "%{$search}%")
                    ->orWhereHas('electricPole', function ($sub) use ($search) {
                        $sub->where('kode', 'like', "%{$search}%");
                    });
                });
            })
            ->paginate(15)
            ->withQueryString();
            
        return view('admin.lampus.index', compact('lampus'));
    }

    public function create()
    {
        $poles = ElectricPole::select('id', 'nomor', 'kode')->get();
        return view('admin.lampus.create', compact('poles'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), $this->validationRules('store'));

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $pole = ElectricPole::findOrFail($request->electric_pole_id);

        $data = $request->except('foto');
        $data['kode'] = $pole->kode . '-' . $data['nomor']; 
        $data['foto_urls'] = $this->fileUploadService->handleMultipleUpload($request, 'foto', 'lampus');

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
        $validator = Validator::make($request->all(), $this->validationRules('update', $lampu));

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $poleId = $request->has('electric_pole_id') ? $request->electric_pole_id : $lampu->electric_pole_id;
        $pole = ElectricPole::findOrFail($poleId);

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
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
        
        return redirect()->route('admin.lampus.index')->with('success', "Lampu '{$lampu->kode}' berhasil diperbarui.");
    }

    public function destroy(Lampu $lampu)
    {
        $this->fileUploadService->deleteMultipleFiles($lampu->foto_urls ?? []);
        $lampu->delete();
        
        return redirect()->route('admin.lampus.index')->with('success', "Lampu '{$lampu->kode}' berhasil dihapus.");
    }
}