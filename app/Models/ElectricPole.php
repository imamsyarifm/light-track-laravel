<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElectricPole extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nomor',
        'kode',
        'provinsi',
        'kode_provinsi',
        'kota_kabupaten',
        'kode_kota_kabupaten',
        'kecamatan',
        'kelurahan_desa',
        'alamat',
        'koordinat',
        'foto_urls',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'foto_urls' => 'array',
    ];

    public function lampus()
    {
        return $this->hasMany(Lampu::class);
    }

    public function iots() 
    {
        return $this->hasMany(Iot::class);
    }

    public function cctvs()
    {
        return $this->hasMany(Cctv::class);
    }

    public function getFotoUrlsAttribute($value)
    {
        if (!$value) return [];
        
        $paths = json_decode($value, true) ?: [];
        
        return array_map(function ($path) {
            return filter_var($path, FILTER_VALIDATE_URL) ? $path : asset('storage/' . $path);
        }, $paths);
    }
}