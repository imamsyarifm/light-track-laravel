<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Iot extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'nomor',
        'electric_pole_id',
        'koordinat',
    ];

    public function electricPole(): BelongsTo
    {
        return $this->belongsTo(ElectricPole::class);
    }
}