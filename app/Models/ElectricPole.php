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
}