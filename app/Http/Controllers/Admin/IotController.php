<?php

namespace App\Http\Controllers\Admin;

use App\Models\Iot;
use App\Models\ElectricPole;
use App\Http\Controllers\Controller;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class IotController extends Controller
{
    protected $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }
    
    private function validationRules(string $method, Iot $iot = null)
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
        //     $rules['nomor'][] = 'unique:iots,nomor';
        // }

        // if ($method === 'update' && $iot) {
        //     $rules['nomor'][] = 'unique:iots,nomor,' . $iot->id;
        // }

        return $rules;
    }

    public function index()
    {
        $search = request('search');
        
        $iots = Iot::with('electricPole')
            ->latest()
            ->when($search, function ($query, $search) {
                return $query->where('nomor', 'like', "%{$search}%")
                             ->orWhere('status', 'like', "%{$search}%")
                             ->orWhereHas('electricPole', function ($q) use ($search) {
                                 $q->where('kode', 'like', "%{$search}%");
                             });
            })
            ->paginate(15)
            ->withQueryString();
            
        return view('admin.iots.index', compact('iots'));
    }

    public function create()
    {
        $poles = ElectricPole::select('id', 'nomor', 'kode')->get();
        return view('admin.iots.create', compact('poles'));
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
        $data['foto_urls'] = $this->fileUploadService->handleMultipleUpload($request, 'foto', 'iots');

        $iot = Iot::create($data);
        
        return redirect()->route('admin.iots.index')->with('success', "IoT '{$data['kode']}' berhasil ditambahkan.");
    }

    public function edit(Iot $iot)
    {
        $poles = ElectricPole::select('id', 'nomor', 'kode')->get();
        return view('admin.iots.edit', compact('iot', 'poles'));
    }
    
    public function update(Request $request, Iot $iot)
    {
        $validator = Validator::make($request->all(), $this->validationRules('update', $iot));

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $poleId = $request->has('electric_pole_id') ? $request->electric_pole_id : $iot->electric_pole_id;
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
                $newUploadedPaths = $this->fileUploadService->handleMultipleUpload($request, 'foto', 'iots');
                $finalPaths = array_merge($finalPaths, $newUploadedPaths);
            }

            $oldPathsInDb = json_decode($iot->getRawOriginal('foto_urls'), true) ?? [];
            $deletedPaths = array_diff($oldPathsInDb, $finalPaths);

            foreach ($deletedPaths as $pathToDelete) {
                if (Storage::disk('public')->exists($pathToDelete)) {
                    Storage::disk('public')->delete($pathToDelete);
                }
            }

            $poleId = $request->has('electric_pole_id') ? $request->electric_pole_id : $iot->electric_pole_id;
            $pole = ElectricPole::findOrFail($poleId);

            $data = $request->except('foto');
            $data['foto_urls'] = $finalPaths;
            $data['kode'] = $pole->kode . '-' . ($data['nomor'] ?? 
            
            $iot->nomor);
            $iot->update($data);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return redirect()->route('admin.iots.index')->with('success', "IoT '{$iot->kode}' berhasil diperbarui.");
    }

    public function destroy(Iot $iot)
    {
        $this->fileUploadService->deleteMultipleFiles($iot->foto_urls ?? []);
        $iot->delete();

        return redirect()->route('admin.iots.index')->with('success', "IoT '{$iot->kode}' berhasil dihapus.");
    }
}