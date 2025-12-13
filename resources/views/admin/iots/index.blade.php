@extends('layouts/contentNavbarLayout')
@section('title', 'Iot')

@section('content')

<div class="container-fluid">
    
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">🛰️ Daftar Iot</h1>
        
        <a href="{{ route('admin.iots.create') }}" class="btn btn-sm btn-primary shadow-sm">
            <i class="mdi mdi-plus-thick mdi-24px"></i> Tambah Iot Baru
        </a>
    </div>
    
    <hr>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="/iot" method="GET" class="form-inline d-flex">
                <input type="text" name="search" class="form-control mr-2" placeholder="Cari Kode atau Nomor Tiang..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-info"><i class="mdi mdi-magnify mdi-24px"></i>Cari</button>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Iot Terpasang</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Kode Iot</th>
                            <th>Nomor Iot</th>
                            <th>Tiang Induk</th>
                            <th>Lokasi (Provinsi/Kota)</th>
                            <th>Koordinat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($iots as $index => $iot)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $iot->kode }}</strong>
                            </td>
                            <td>{{ $iot->nomor }}</td>
                            <td>
                                @if ($iot->electricPole)
                                    <span class="badge badge-info">{{ $iot->electricPole->kode }}</span>
                                @else
                                    <span class="text-danger">Tiang Dihapus</span>
                                @endif
                            </td>
                            <td>
                                @if ($iot->electricPole)
                                    {{ $iot->electricPole->provinsi }} / {{ $iot->electricPole->kota_kabupaten }}
                                @else
                                    -
                                @endif
                            </td>
                             <td>{{ $iot->koordinat ?? '-' }}</td>
                            <td>
                                <a href="{{ route('admin.iots.edit', $iot->id) }}" class="btn btn-sm btn-warning mb-1 px-2 py-1">
                                    <i class="mdi mdi-square-edit-outline mdi-24px"></i>
                                </a>
                                <form action="{{ route('admin.iots.destroy', $iot->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data iot ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger mb-1 px-2 py-1">
                                        <i class="mdi mdi-delete-outline mdi-24px"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Belum ada data iot yang tercatat.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if (method_exists($iots, 'links'))
                <div class="mt-3">
                    {{ $iots->links() }}
                </div>
            @endif
            
        </div>
    </div>

</div>

@endsection


