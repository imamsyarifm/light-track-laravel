@extends('layouts/contentNavbarLayout')
@section('title', 'Detail Tiang - ' . $pole->kode)

@section('content')
<div class="container-fluid">
    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0">ID Tiang: {{ $pole->kode }}</h4>
        </div>
        <div>
            <a href="{{ route('admin.poles.index') }}" class="btn btn-secondary">Kembali</a>
            <a href="{{ route('admin.poles.edit', $pole->id) }}" class="btn btn-warning">Edit Data</a>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8 col-lg-7">
            <div class="card mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold">Informasi Teknis & Lokasi</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="30%">Nomor Tiang</th>
                            <td>: {{ $pole->nomor }}</td>
                        </tr>
                        <tr>
                            <th>Provinsi</th>
                            <td>: {{ $pole->provinsi }}</td>
                        </tr>
                        <tr>
                            <th>Kota/Kabupaten</th>
                            <td>: {{ $pole->kota_kabupaten }}</td>
                        </tr>
                        <tr>
                            <th>Kecamatan/Kelurahan</th>
                            <td>: {{ $pole->kecamatan }} / {{ $pole->kelurahan_desa }}</td>
                        </tr>
                        <tr>
                            <th>Alamat Lengkap</th>
                            <td>: {{ $pole->alamat }}</td>
                        </tr>
                        <tr>
                            <th>Koordinat</th>
                            <td>: <span class="badge bg-label-info">{{ $pole->koordinat ?? 'Tidak ada data' }}</span></td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold">Daftar Perangkat Terhubung</h6>
                    <span class="badge bg-primary">{{ $pole->lampus->count() + $pole->iots->count() + $pole->cctvs->count() }} Total Perangkat</span>
                </div>
                <div class="nav-align-top">
                    <ul class="nav nav-tabs nav-fill" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-lampu">
                                Lampu <span class="badge rounded-pill bg-label-info ms-1">{{ $pole->lampus->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-iot">
                                IoT <span class="badge rounded-pill bg-label-info ms-1">{{ $pole->iots->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-cctv">
                                CCTV <span class="badge rounded-pill bg-label-info ms-1">{{ $pole->cctvs->count() }}</span>
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content border-0">
                        <div class="tab-pane fade show active" id="tab-lampu" role="tabpanel">
                            @include('admin.poles.partials.device-table', ['devices' => $pole->lampus, 'type' => 'lampus'])
                        </div>
                        <div class="tab-pane fade" id="tab-iot" role="tabpanel">
                            @include('admin.poles.partials.device-table', ['devices' => $pole->iots, 'type' => 'iots'])
                        </div>
                        <div class="tab-pane fade" id="tab-cctv" role="tabpanel">
                            @include('admin.poles.partials.device-table', ['devices' => $pole->cctvs, 'type' => 'cctvs'])
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-5">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Galeri Foto</h5>
                    <small class="text-muted">{{ count($pole->foto_urls ?? []) }} Foto</small>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        @forelse($pole->foto_urls ?? [] as $url)
                        <div class="col-6">
                            <a href="{{ asset($url) }}" target="_blank" class="d-block shadow-xs rounded border overflow-hidden">
                                <img src="{{ asset($url) }}" class="img-fluid" style="height: 120px; width: 100%; object-fit: cover;">
                            </a>
                        </div>
                        @empty
                        <div class="col-12 text-center py-4 bg-light rounded border-dashed">
                            <i class='bx bx-image-alt fs-1 text-muted'></i>
                            <p class="mb-0">Tidak ada foto</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            @if($pole->koordinat)
            <div class="card border-primary">
                <div class="card-body text-center py-3">
                    <p class="small mb-2">Lihat lokasi di peta eksternal</p>
                    <a href="https://www.google.com/maps/search/?api=1&query={{ $pole->koordinat }}" target="_blank" class="btn btn-primary w-100">
                        <i class='bx bxl-google me-1'></i> Google Maps
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection