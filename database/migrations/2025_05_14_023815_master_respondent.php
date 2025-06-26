<?php

use GuzzleHttp\Promise\Create;
use GuzzleHttp\Psr7\DroppingStream;
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
        if (!Schema::hasTable('master_respondent')) { // untuk mengecek apakah tabel sudah ada sehingga tidak membuat tabel yang sama dan tidak membuat error migrasi
            // jika tabel belum ada, maka buat tabel baru
            Schema::create('master_respondent', function (Blueprint $table) {
                $table->id(); // ID primary key auto-increment
                $table->string('nama_respondent')->nullable();
                $table->string('email_respondent')->unique()->nullable();
                $table->string('telepone_respondent')->unique()->nullable();
                $table->string('nama_toko_respondent')->nullable();
                $table->string('alamat_toko_respondent')->nullable();

                $table->unsignedBigInteger('provinsi_id')->nullable();
                $table->unsignedBigInteger('kota_id')->nullable();
                $table->unsignedBigInteger('jenis_pertanyaan_id')->nullable();

                $table->timestamps();

                $table->foreign('provinsi_id')->references('id')->on('master_provinsi')->onDelete('cascade');
                $table->foreign('kota_id')->references('id')->on('master_kota_survey')->onDelete('cascade');
                $table->foreign('jenis_pertanyaan_id')->references('id')->on('master_jenis_pertanyaan')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('master_respondent'); // jika tabel ada, maka hapus tabel
    }
};
