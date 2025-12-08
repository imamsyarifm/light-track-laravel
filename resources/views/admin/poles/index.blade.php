@extends('layouts/contentNavbarLayout')
@section('title', 'Tiang Lampu')

@section('content')

<div class="container-fluid">
    
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">📋 Daftar Tiang Listrik</h1>
        
        <a href="{{ route('admin.poles.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Tiang Baru
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
            <form action="{{ route('admin.poles.index') }}" method="GET" class="form-inline">
                <input type="text" name="search" class="form-control mr-2" placeholder="Cari Nomor atau Provinsi..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-info">Cari</button>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Tiang Listrik</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Kode</th>
                            <th>Nomor</th>
                            <th>Provinsi & Kota</th>
                            <th>Alamat</th>
                            <th>Foto</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($poles as $index => $pole)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $pole->kode }}</strong> <br>
                                <small>({{ $pole->kode_provinsi . ' / ' . $pole->kode_kota_kabupaten }})</small>
                            </td>
                            <td>{{ $pole->nomor }}</td>
                            <td>{{ $pole->provinsi }} - {{ $pole->kota_kabupaten }}</td>
                            <td>{{ Str::limit($pole->alamat, 50) }}</td>
                            <td>
                                @if ($pole->foto_url)
                                    <img src="{{ $pole->foto_url }}" alt="Foto Tiang" style="width: 50px; height: 50px; object-fit: cover;">
                                @else
                                    <i class="fas fa-image text-muted"></i>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.poles.edit', $pole->id) }}" class="btn btn-sm btn-warning mb-1">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.poles.destroy', $pole->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tiang ini? Data relasional seperti Lampu, IoT, dan CCTV juga akan terhapus!');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger mb-1">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Belum ada data Tiang Listrik yang tercatat.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if (method_exists($poles, 'links'))
                <div class="mt-3">
                    {{ $poles->links() }}
                </div>
            @endif
            
        </div>
    </div>

</div>

@endsection