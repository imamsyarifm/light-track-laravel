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
        Schema::table('electric_poles', function (Blueprint $table) {
            if (Schema::hasColumn('electric_poles', 'foto_url')) {
                $table->dropColumn('foto_url'); 
            }
            $table->json('foto_urls')->nullable()->after('koordinat');
        });
        
        Schema::table('lampus', function (Blueprint $table) {
            if (Schema::hasColumn('lampus', 'foto_url')) {
                $table->dropColumn('foto_url'); 
            }
            $table->json('foto_urls')->nullable()->after('koordinat');
        });
        
        Schema::table('iots', function (Blueprint $table) {
            if (Schema::hasColumn('iots', 'foto_url')) {
                $table->dropColumn('foto_url'); 
            }
            $table->json('foto_urls')->nullable()->after('koordinat');
        });

        Schema::table('cctvs', function (Blueprint $table) {
            if (Schema::hasColumn('cctvs', 'foto_url')) {
                $table->dropColumn('foto_url'); 
            }
            $table->json('foto_urls')->nullable()->after('koordinat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('electric_poles', function (Blueprint $table) {
            if (Schema::hasColumn('electric_poles', 'foto_urls')) {
                $table->dropColumn('foto_urls');
            }
            $table->string('foto_url')->nullable(); 
        });
        
        Schema::table('lampus', function (Blueprint $table) {
            if (Schema::hasColumn('lampus', 'foto_urls')) {
                $table->dropColumn('foto_urls');
            }
            $table->string('foto_url')->nullable(); 
        });

        Schema::table('iots', function (Blueprint $table) {
            if (Schema::hasColumn('iots', 'foto_urls')) {
                $table->dropColumn('foto_urls');
            }
            $table->string('foto_url')->nullable(); 
        });

        Schema::table('cctvs', function (Blueprint $table) {
            if (Schema::hasColumn('cctvs', 'foto_urls')) {
                $table->dropColumn('foto_urls');
            }
            $table->string('foto_url')->nullable(); 
        });
    }
};