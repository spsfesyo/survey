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
        if (!Schema::hasTable('periode_survey')) { // untuk mengecek apakah tabel sudah ada sehingga tidak membuat tabel yang sama dan tidak membuat error migrasi
            // jika tabel belum ada, maka buat tabel baru
            Schema::create('periode_survey', function (Blueprint $table) {
                $table->id(); // ID primary key auto-increment
                $table->string('nama_survey')->nullable();
                $table->date('start_at')->nullable();
                $table->date('end_at')->nullable();
                $table->enum('status', ['active', 'inactive'])->nullable()->default(null); // status survey, bisa aktif atau tidak aktif

                $table->timestamps();

            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::dropIfExists('periode_survey'); // jika tabel ada, maka hapus tabel
    }
};
