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
        Schema::create('master_area', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_provinsi_id')
                      ->nullable()
                      ->constrained('master_provinsi')
                      ->nullOnDelete()
                      ->after('id');
            $table->string('nama_area')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_area_id');
    }
};
