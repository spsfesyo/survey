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
        if (!Schema::hasTable('answer_survey')) { // untuk mengecek apakah tabel sudah ada sehingga tidak membuat tabel yang sama dan tidak membuat error migrasi

            Schema::create('answer_survey', function (Blueprint $table) {
                $table->id(); // ID primary key auto-increment
                $table->unsignedBigInteger('master_respondent_id')->nullable();
                $table->unsignedBigInteger('master_pertanyaan_id')->nullable();
                $table->unsignedBigInteger('pertanyaan_options_id')->nullable();
                $table->text('jawaban_teks')->nullable(); // kolom jawaban
                $table->text('lainnya')->nullable(); // kolom lainnya
                $table->timestamps();

                $table->foreign('master_respondent_id')->references('id')->on('master_respondent')->onDelete('cascade');
                $table->foreign('master_pertanyaan_id')->references('id')->on('master_pertanyaan')->onDelete('cascade');
                $table->foreign('pertanyaan_options_id')->references('id')->on('pertanyaan_options')->onDelete('cascade');
            });

        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('answer_survey'); // jika tabel ada, maka hapus tabel
    }
};
