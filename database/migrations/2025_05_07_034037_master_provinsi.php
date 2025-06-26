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
        Schema::create('master_provinsi', function (Blueprint $table) {
            $table->id()->index()->autoIncrement(); // kolom id (auto increment)
            $table->string('nama_provinsi'); // kolom nama_provinsi
            $table->timestamps(); // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_provinsi');
    }
};
