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
        'provinsi',
        'kota_kabupaten',
        'kecamatan',
        'kelurahan_desa',
        'alamat',
        'koordinat',
    ];
}
