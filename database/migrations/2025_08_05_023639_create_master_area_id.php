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
        // Sederhana: Hanya buat tabel jika tabel 'master_area' BELUM ada
        if (!Schema::hasTable('master_area')) {
            Schema::create('master_area', function (Blueprint $table) {
                $table->id();
                $table->foreignId('master_provinsi_id')
                    ->nullable()
                    ->constrained('master_provinsi')
                    ->nullOnDelete();
                $table->string('nama_area')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_area_id');
    }
};
