<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lampu extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'nomor',
        'kode',
        'electric_pole_id',
        'koordinat',
        'foto_urls',
    ];

    protected $casts = [
        'foto_urls' => 'array',
    ];

    public function electricPole(): BelongsTo
    {
        return $this->belongsTo(ElectricPole::class);
    }
}
