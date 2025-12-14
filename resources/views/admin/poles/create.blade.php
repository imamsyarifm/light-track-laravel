@extends('layouts/contentNavbarLayout')
@section('title', 'Create Tiang Lampu')

@section('content')

<div class="container-fluid">
    
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tambah Tiang Listrik Baru</h1>
    </div>
    
    <hr>

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    
    <div class="card shadow mb-4">
        <div class="card-body">
            
            <form action="{{ route('admin.poles.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nomor">Nomor Tiang Listrik</label>
                            <input type="text" name="nomor" id="nomor" class="form-control @error('nomor') is-invalid @enderror" 
                                   value="{{ old('nomor') }}" required placeholder="Contoh: T123456">
                            @error('nomor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <h5 class="mt-4 mb-3">Data Lokasi (Kode Wilayah)</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="provinsi">Provinsi</label>
                            <input type="text" name="provinsi" id="provinsi" class="form-control @error('provinsi') is-invalid @enderror" 
                                   value="{{ old('provinsi') }}" required placeholder="Contoh: JAWA BARAT">
                            @error('provinsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="kota_kabupaten">Kota/Kabupaten</label>
                            <input type="text" name="kota_kabupaten" id="kota_kabupaten" class="form-control @error('kota_kabupaten') is-invalid @enderror" 
                                   value="{{ old('kota_kabupaten') }}" required placeholder="Contoh: KOTA BANDUNG">
                            @error('kota_kabupaten')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="kecamatan">Kecamatan</label>
                            <input type="text" name="kecamatan" id="kecamatan" class="form-control @error('kecamatan') is-invalid @enderror" 
                                   value="{{ old('kecamatan') }}" required>
                            @error('kecamatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="kelurahan_desa">Kelurahan/Desa</label>
                            <input type="text" name="kelurahan_desa" id="kelurahan_desa" class="form-control @error('kelurahan_desa') is-invalid @enderror" 
                                   value="{{ old('kelurahan_desa') }}" required>
                            @error('kelurahan_desa')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="koordinat">Koordinat (Contoh: -6.8911, 107.6105)</label>
                            <input type="text" name="koordinat" id="koordinat" class="form-control @error('koordinat') is-invalid @enderror" 
                                   value="{{ old('koordinat') }}">
                            @error('koordinat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="alamat">Alamat Lengkap</label>
                    <textarea name="alamat" id="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="3" required>{{ old('alamat') }}</textarea>
                    @error('alamat')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <h5 class="mt-4 mb-3">Foto Tiang</h5>
                <div class="form-group">
                    <label for="foto">Unggah Foto Tiang (Maks. 4 Foto, Max 2MB/foto)</label>
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
                
                <button type="submit" class="btn btn-primary mt-3">Simpan Data Tiang</button>
                <a href="{{ route('admin.poles.index') }}" class="btn btn-secondary mt-3">Batal</a>

            </form>
        </div>
    </div>

</div>

@endsection