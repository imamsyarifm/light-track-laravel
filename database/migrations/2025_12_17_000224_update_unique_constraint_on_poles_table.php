<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('electric_poles', function (Blueprint $table) {
            $table->dropUnique(['nomor']); 
            $table->unique(['nomor', 'kota_kabupaten']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('electric_poles', function (Blueprint $table) {
            $table->dropUnique(['nomor', 'kota_kabupaten']);
            $table->unique('nomor');
        });
    }
};