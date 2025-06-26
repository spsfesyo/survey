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
        if (!Schema::hasTable('master_pertanyaan')) {
            Schema::create('master_pertanyaan', function (Blueprint $table) {
                $table->id()->index()->autoIncrement(); // kolom id (auto increment)
                $table->text('pertanyaan')->nullable(); // kolom pertanyaan
                $table->unsignedBigInteger('master_jenis_pertanyaan_id')->nullable();  // kolom jenis_pertanyaan_id
                $table->unsignedBigInteger('master_section_id')->nullable();
                $table->unsignedBigInteger('master_tipe_pertanyaan_id')->nullable();
                $table->integer('order')->nullable(); // kolom order
                $table->timestamps(); // created_at & updated_at

                $table->foreign('master_jenis_pertanyaan_id')->references('id')->on('master_jenis_pertanyaan')->onDelete('cascade');
                $table->foreign('master_section_id')->references('id')->on('master_section')->onDelete('cascade');
                $table->foreign('master_tipe_pertanyaan_id')->references('id')->on('master_tipe_pertanyaan')->onDelete('cascade');
                $table->text('reference')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_pertanyaan'); // jika tabel ada, maka hapus tabel
    }
};
