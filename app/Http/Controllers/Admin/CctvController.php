<?php

namespace App\Http\Controllers\Admin;

use App\Models\Cctv;
use App\Models\ElectricPole;
use App\Http\Controllers\Controller;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class CctvController extends Controller
{
    protected $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }
    
    private function validationRules(string $method, Cctv $cctv = null)
    {
        $rules = [
            'electric_pole_id' => ['required', 'exists:electric_poles,id'],
            'nomor'            => ['required', 'string', 'max:255'],
            'koordinat'        => ['nullable', 'string', 'max:255'],
            'foto'             => ['nullable', 'array', 'max:4'],
            'foto.*'           => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
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
        $search = request('search');
        
        $cctvs = Cctv::with('electricPole')
            ->latest()
            ->when($search, function ($query, $search) {
                return $query->where('nomor', 'like', "%{$search}%")
                             ->orWhere('merk', 'like', "%{$search}%")
                             ->orWhereHas('electricPole', function ($q) use ($search) {
                                 $q->where('kode', 'like', "%{$search}%");
                             });
            })
            ->paginate(15)
            ->withQueryString();
            
        return view('admin.cctvs.index', compact('cctvs'));
    }

    public function create()
    {
        $poles = ElectricPole::select('id', 'nomor', 'kode')->get();
        return view('admin.cctvs.create', compact('poles'));
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
        $data['foto_urls'] = $this->fileUploadService->handleMultipleUpload($request, 'foto', 'cctvs');

        $cctv = Cctv::create($data);
        
        return redirect()->route('admin.cctvs.index')->with('success', "CCTV '{$data['kode']}' berhasil ditambahkan.");
    }

    public function edit(Cctv $cctv)
    {
        $poles = ElectricPole::select('id', 'nomor', 'kode')->get();
        return view('admin.cctvs.edit', compact('cctv', 'poles'));
    }
    
    public function update(Request $request, Cctv $cctv)
    {
        $validator = Validator::make($request->all(), $this->validationRules('update', $cctv));

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $poleId = $request->has('electric_pole_id') ? $request->electric_pole_id : $cctv->electric_pole_id;
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
                $newUploadedPaths = $this->fileUploadService->handleMultipleUpload($request, 'foto', 'cctvs');
                $finalPaths = array_merge($finalPaths, $newUploadedPaths);
            }

            $oldPathsInDb = json_decode($cctv->getRawOriginal('foto_urls'), true) ?? [];
            $deletedPaths = array_diff($oldPathsInDb, $finalPaths);

            foreach ($deletedPaths as $pathToDelete) {
                if (Storage::disk('public')->exists($pathToDelete)) {
                    Storage::disk('public')->delete($pathToDelete);
                }
            }

            $poleId = $request->has('electric_pole_id') ? $request->electric_pole_id : $cctv->electric_pole_id;
            $pole = ElectricPole::findOrFail($poleId);

            $data = $request->except('foto');
            $data['foto_urls'] = $finalPaths;
            $data['kode'] = $pole->kode . '-' . ($data['nomor'] ?? 
            
            $cctv->nomor);
            $cctv->update($data);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return redirect()->route('admin.cctvs.index')->with('success', "CCTV '{$cctv->kode}' berhasil diperbarui.");
    }

    public function destroy(Cctv $cctv)
    {
        $this->fileUploadService->deleteMultipleFiles($cctv->foto_urls ?? []);
        $cctv->delete();
        
        return redirect()->route('admin.cctvs.index')->with('success', "CCTV '{$cctv->kode}' berhasil dihapus.");
    }
}