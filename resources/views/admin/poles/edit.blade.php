{{-- @extends('layouts.admin') --}}

@section('content')

<div class="container-fluid">
    
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">✏️ Edit Tiang Listrik: {{ $pole->nomor }}</h1>
        <small class="text-muted">Kode: {{ $pole->kode }}</small>
    </div>
    
    <hr>

    {{-- Pesan Feedback Global --}}
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    
    <div class="card shadow mb-4">
        <div class="card-body">
            
            {{-- PENTING: Gunakan method POST dengan directive @method('PUT') --}}
            <form action="{{ route('admin.poles.update', $pole->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT') 
                
                {{-- Bagian 1: Data Identitas Tiang --}}
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nomor">Nomor Tiang Listrik</label>
                            {{-- Field diisi dengan data lama ($pole->nomor) atau input yang gagal (old()) --}}
                            <input type="text" name="nomor" id="nomor" class="form-control @error('nomor') is-invalid @enderror" 
                                   value="{{ old('nomor', $pole->nomor) }}" required placeholder="Contoh: T123456">
                            @error('nomor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Bagian 2: Data Lokasi Geografis --}}
                <h5 class="mt-4 mb-3">📍 Data Lokasi (Kode Wilayah)</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="provinsi">Provinsi</label>
                            <input type="text" name="provinsi" id="provinsi" class="form-control @error('provinsi') is-invalid @enderror" 
                                   value="{{ old('provinsi', $pole->provinsi) }}" required placeholder="Contoh: JAWA BARAT">
                            @error('provinsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="kota_kabupaten">Kota/Kabupaten</label>
                            <input type="text" name="kota_kabupaten" id="kota_kabupaten" class="form-control @error('kota_kabupaten') is-invalid @enderror" 
                                   value="{{ old('kota_kabupaten', $pole->kota_kabupaten) }}" required placeholder="Contoh: KOTA BANDUNG">
                            @error('kota_kabupaten')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Bagian 3: Detail Alamat & Koordinat --}}
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="kecamatan">Kecamatan</label>
                            <input type="text" name="kecamatan" id="kecamatan" class="form-control @error('kecamatan') is-invalid @enderror" 
                                   value="{{ old('kecamatan', $pole->kecamatan) }}" required>
                            @error('kecamatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="kelurahan_desa">Kelurahan/Desa</label>
                            <input type="text" name="kelurahan_desa" id="kelurahan_desa" class="form-control @error('kelurahan_desa') is-invalid @enderror" 
                                   value="{{ old('kelurahan_desa', $pole->kelurahan_desa) }}" required>
                            @error('kelurahan_desa')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="koordinat">Koordinat</label>
                            <input type="text" name="koordinat" id="koordinat" class="form-control @error('koordinat') is-invalid @enderror" 
                                   value="{{ old('koordinat', $pole->koordinat) }}">
                            @error('koordinat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="alamat">Alamat Lengkap</label>
                    <textarea name="alamat" id="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="3" required>{{ old('alamat', $pole->alamat) }}</textarea>
                    @error('alamat')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Bagian 4: Upload Foto (dengan tampilan foto lama) --}}
                <h5 class="mt-4 mb-3">🖼️ Foto Tiang</h5>
                @if ($pole->foto_url)
                    <div class="mb-3">
                        <label>Foto Saat Ini:</label><br>
                        <img src="{{ $pole->foto_url }}" alt="Foto Tiang Lama" style="max-width: 200px; height: auto; border: 1px solid #ccc;">
                    </div>
                @endif
                
                <div class="form-group">
                    <label for="foto">Ganti Foto Tiang (Biarkan kosong jika tidak ingin mengganti)</label>
                    <input type="file" name="foto" id="foto" class="form-control-file @error('foto') is-invalid @enderror">
                    @error('foto')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <hr>
                
                {{-- Tombol Submit --}}
                <button type="submit" class="btn btn-success mt-3"><i class="fas fa-save"></i> Perbarui Data</button>
                <a href="{{ route('admin.poles.index') }}" class="btn btn-secondary mt-3">Kembali ke Daftar</a>

            </form>
            
        </div>
    </div>

</div>

@endsection