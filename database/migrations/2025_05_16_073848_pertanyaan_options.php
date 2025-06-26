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
        if (!Schema::hasTable('pertanyaan_options')) { // untuk mengecek apakah tabel sudah ada sehingga tidak membuat tabel yang sama dan tidak membuat error migrasi

            Schema::create('pertanyaan_options', function (Blueprint $table) {
                $table->id(); // ID primary key auto-increment
                $table->unsignedBigInteger('master_pertanyaan_id')->nullable();
                $table->string('options')->nullable();
                $table->boolean('is_other')->default(false); // kolom is_other

                $table->foreign('master_pertanyaan_id')->references('id')->on('master_pertanyaan')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pertanyaan_options'); // jika tabel ada, maka hapus tabel
    }
};
