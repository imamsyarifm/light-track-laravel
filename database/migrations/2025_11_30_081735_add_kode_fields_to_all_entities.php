<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('electric_poles', function (Blueprint $table) {
            $table->string('kode')->unique()->nullable()->after('nomor'); 
            $table->string('kode_provinsi', 2)->nullable()->after('provinsi');
            $table->string('kode_kota_kabupaten', 2)->nullable()->after('kota_kabupaten');
        });

        Schema::table('lampus', function (Blueprint $table) {
            $table->string('kode')->unique()->nullable()->after('nomor');
        });

        Schema::table('iots', function (Blueprint $table) {
            $table->string('kode')->unique()->nullable()->after('nomor');
        });

        Schema::table('cctvs', function (Blueprint $table) {
            $table->string('kode')->unique()->nullable()->after('nomor');
        });
    }

    public function down(): void
    {
        Schema::table('electric_poles', function (Blueprint $table) {
            $table->dropColumn(['kode_provinsi', 'kode_kota_kabupaten', 'kode']);
        });
        Schema::table('lampus', function (Blueprint $table) {
            $table->dropColumn('kode');
        });
        Schema::table('iots', function (Blueprint $table) {
            $table->dropColumn('kode');
        });
        Schema::table('cctvs', function (Blueprint $table) {
            $table->dropColumn('kode');
        });
    }
};