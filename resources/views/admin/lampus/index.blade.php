{{-- @extends('layouts.admin') --}}

@section('content')

<div class="container-fluid">
    
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">💡 Daftar Lampu</h1>
        
        <a href="{{ route('admin.lampus.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Lampu Baru
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
            <form action="{{ route('admin.lampus.index') }}" method="GET" class="form-inline">
                <input type="text" name="search" class="form-control mr-2" placeholder="Cari Kode atau Nomor Tiang..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-info">Cari</button>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Lampu Terpasang</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Kode Lampu</th>
                            <th>Nomor Lampu</th>
                            <th>Tiang Induk</th>
                            <th>Lokasi (Provinsi/Kota)</th>
                            <th>Koordinat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($lampus as $index => $lampu)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $lampu->kode }}</strong>
                            </td>
                            <td>{{ $lampu->nomor }}</td>
                            <td>
                                @if ($lampu->electricPole)
                                    <span class="badge badge-info">{{ $lampu->electricPole->kode }}</span>
                                @else
                                    <span class="text-danger">Tiang Dihapus</span>
                                @endif
                            </td>
                            <td>
                                @if ($lampu->electricPole)
                                    {{ $lampu->electricPole->provinsi }} / {{ $lampu->electricPole->kota_kabupaten }}
                                @else
                                    -
                                @endif
                            </td>
                             <td>{{ $lampu->koordinat ?? '-' }}</td>
                            <td>
                                <a href="{{ route('admin.lampus.edit', $lampu->id) }}" class="btn btn-sm btn-warning mb-1">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.lampus.destroy', $lampu->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data Lampu ini?');">
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
                            <td colspan="7" class="text-center">Belum ada data Lampu yang tercatat.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if (method_exists($lampus, 'links'))
                <div class="mt-3">
                    {{ $lampus->links() }}
                </div>
            @endif
            
        </div>
    </div>

</div>

@endsection