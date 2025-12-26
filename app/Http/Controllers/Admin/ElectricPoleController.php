<?php

namespace App\Http\Controllers\Admin;

use App\Models\ElectricPole;
use App\Http\Controllers\Controller;
use App\Services\FileUploadService;
use App\Services\WilayahService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class ElectricPoleController extends Controller
{
    protected $fileUploadService;
    protected $wilayahService;

    public function __construct(FileUploadService $fileUploadService, WilayahService $wilayahService)
    {
        $this->fileUploadService = $fileUploadService;
        $this->wilayahService = $wilayahService;
    }

    private function validationRules(string $method, ElectricPole $pole = null)
    {
        $rules = [
            'nomor'          => ['required', 'string', 'max:255'],
            'provinsi'       => ['required', 'string', 'max:255'],
            'kota_kabupaten' => ['required', 'string', 'max:255'],
            'kecamatan'      => ['required', 'string', 'max:255'],
            'kelurahan_desa' => ['required', 'string', 'max:255'],
            'alamat'         => ['required', 'string'],
            'koordinat'      => ['nullable', 'string', 'max:255'],
            'foto'           => ['nullable', 'array', 'max:4'],
            'foto.*'         => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ];

        // Notes: temporary not using unique rules
        // if ($method === 'store') {
        //     $rules['nomor'][] = 'unique:electric_poles,nomor';
        // }

        // if ($method === 'update' && $pole) {
        //     $rules['nomor'][] = 'unique:electric_poles,nomor,' . $pole->id;
        // }

        return $rules;
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
                    $query->where(function ($q) use ($search) {
                        $q->where('nomor', 'like', "%{$search}%")
                        ->orWhere('provinsi', 'like', "%{$search}%")
                        ->orWhere('kota_kabupaten', 'like', "%{$search}%");
                    });
                })
                ->paginate(15);

        return view('admin.poles.index', compact('poles'));
    }

    public function show($id)
    {
        $pole = ElectricPole::with(['lampus', 'iots', 'cctvs'])->findOrFail($id);
        return view('admin.poles.show', compact('pole'));
    }

    public function create()
    {
        return view('admin.poles.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), $this->validationRules('store'));

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        try {
            $data = $this->generateKode($request->except('foto'));
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        $data['foto_urls'] = $this->fileUploadService->handleMultipleUpload($request, 'foto', 'electric_poles');

        ElectricPole::create($data);

        return redirect()->route('admin.poles.index')->with('success', "Tiang Listrik '{$data['kode']}' berhasil ditambahkan.");
    }

    public function edit(ElectricPole $pole)
    {
        return view('admin.poles.edit', compact('pole'));
    }
    
    public function update(Request $request, ElectricPole $pole)
    {
        $validator = Validator::make($request->all(), $this->validationRules('update', $pole));

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $data = $this->generateKode($request->except('foto'));
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

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
                $newUploadedPaths = $this->fileUploadService->handleMultipleUpload($request, 'foto', 'electric_poles');
                $finalPaths = array_merge($finalPaths, $newUploadedPaths);
            }

            $oldPathsInDb = json_decode($pole->getRawOriginal('foto_urls'), true) ?? [];
            $deletedPaths = array_diff($oldPathsInDb, $finalPaths);

            foreach ($deletedPaths as $pathToDelete) {
                if (Storage::disk('public')->exists($pathToDelete)) {
                    Storage::disk('public')->delete($pathToDelete);
                }
            }

            $data = $request->except('foto');
            $data['foto_urls'] = $finalPaths;
            $data = $this->generateKode($data);

            $pole->update($data);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }

        return redirect()->route('admin.poles.index')->with('success', "Tiang Listrik '{$pole->kode}' berhasil diperbarui.");
    }
    
    public function destroy(ElectricPole $pole)
    {
        $this->fileUploadService->deleteMultipleFiles($pole->foto_urls ?? []);
        $pole->delete();
        
        return redirect()->route('admin.poles.index')->with('success', "Tiang Listrik '{$pole->kode}' berhasil dihapus.");
    }
}