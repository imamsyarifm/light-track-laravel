@extends('layouts/contentNavbarLayout')
@section('title', 'Create Iot')

@section('content')

<div class="container-fluid">
    
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tambah Iot Baru</h1>
    </div>
    
    <hr>

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    
    <div class="card shadow mb-4">
        <div class="card-body">
            
            <form action="{{ route('admin.iots.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <h5 class="mt-4 mb-3">Tiang Induk (Wajib)</h5>
                <div class="form-group">
                    <label for="electric_pole_id">Pilih Tiang Listrik Induk</label>
                    <select name="electric_pole_id" id="electric_pole_id" class="form-control @error('electric_pole_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Tiang Listrik --</option>
                        @foreach ($poles as $pole)
                            <option value="{{ $pole->id }}" {{ old('electric_pole_id') == $pole->id ? 'selected' : '' }}>
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
                                   value="{{ old('nomor') }}" required placeholder="Contoh: IOT001 (Huruf Kapital)">
                            @error('nomor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                         <div class="form-group">
                            <label for="koordinat">Koordinat (Contoh: -6.8911, 107.6105)</label>
                            <input type="text" name="koordinat" id="koordinat" class="form-control @error('koordinat') is-invalid @enderror" 
                                   value="{{ old('koordinat') }}" placeholder="Opsional">
                            @error('koordinat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <h5 class="mt-4 mb-3">Foto Iot</h5>
                <div class="form-group">
                    <label for="foto">Unggah Foto Iot (Maks. 4 Foto, Max 2MB/foto)</label>
                    <input type="file" name="foto[]" id="foto" class="form-control-file @error('foto') is-invalid @enderror @error('foto.*') is-invalid @enderror" 
                           multiple>
                    <small class="form-text text-muted">Anda dapat memilih hingga 4 foto sekaligus.</small>
                    @error('foto')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    @error('foto.*') 
                        <div class="invalid-feedback d-block">{{ $message }}</div> 
                    @enderror
                </div>
                
                <hr>
                
                <button type="submit" class="btn btn-primary mt-3">Simpan Data Iot</button>
                <a href="{{ route('admin.iots.index') }}" class="btn btn-secondary mt-3">Batal</a>

            </form>
            
        </div>
    </div>

</div>

@endsection