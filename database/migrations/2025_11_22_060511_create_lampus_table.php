<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lampus', function (Blueprint $table) {
            $table->id();
            $table->string('nomor')->unique();
            
            $table->foreignId('electric_pole_id')
                  ->constrained()
                  ->onDelete('cascade');
                  
            $table->string('koordinat')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lampus');
    }
};
