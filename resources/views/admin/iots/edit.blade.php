@extends('layouts/contentNavbarLayout')
@section('title', 'Edit Iot')

@section('content')

<div class="container-fluid">
    
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">✏️ Edit Iot: {{ $iot->nomor }}</h1>
        <small class="text-muted">Kode: {{ $iot->kode }}</small>
    </div>
    
    <hr>

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    
    <div class="card shadow mb-4">
        <div class="card-body">
            
            <form action="{{ route('admin.iots.update', $iot->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT') 
                
                <h5 class="mt-4 mb-3">Tiang Induk (Mengubah Tiang akan Mengubah Kode Iot)</h5>
                <div class="form-group">
                    <label for="electric_pole_id">Pilih Tiang Listrik Induk</label>
                    <select name="electric_pole_id" id="electric_pole_id" class="form-control @error('electric_pole_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Tiang Listrik --</option>
                        @foreach ($poles as $pole)
                            <option value="{{ $pole->id }}" 
                                {{ old('electric_pole_id', $iot->electric_pole_id) == $pole->id ? 'selected' : '' }}>
                                {{ $pole->kode }} (Nomor: {{ $pole->nomor ?? 'N/A' }})
                            </option>
                        @endforeach
                    </select>
                    @error('electric_pole_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <h5 class="mt-4 mb-3">Detail Iot</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nomor">Nomor Iot</label>
                            <input type="text" name="nomor" id="nomor" class="form-control @error('nomor') is-invalid @enderror" 
                                   value="{{ old('nomor', $iot->nomor) }}" required placeholder="Contoh: LA001">
                            @error('nomor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                         <div class="form-group">
                            <label for="koordinat">Koordinat</label>
                            <input type="text" name="koordinat" id="koordinat" class="form-control @error('koordinat') is-invalid @enderror" 
                                   value="{{ old('koordinat', $iot->koordinat) }}" placeholder="Opsional">
                            @error('koordinat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <h5 class="mt-4 mb-3">Foto Iot</h5>
                @if ($iot->foto_url)
                    <div class="mb-3">
                        <label>Foto Saat Ini:</label><br>
                        <img src="{{ $iot->foto_url }}" alt="Foto Iot Lama" style="max-width: 200px; height: auto; border: 1px solid #ccc;">
                    </div>
                @endif
                
                <div class="form-group">
                    <label for="foto">Ganti Foto Iot (Biarkan kosong jika tidak ingin mengganti)</label>
                    <input type="file" name="foto" id="foto" class="form-control-file @error('foto') is-invalid @enderror">
                    @error('foto')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <hr>
                
                <button type="submit" class="btn btn-success mt-3"><i class="fas fa-save"></i> Perbarui Data</button>
                <a href="{{ route('admin.iots.index') }}" class="btn btn-secondary mt-3">Kembali ke Daftar</a>

            </form>
            
        </div>
    </div>

</div>

@endsection