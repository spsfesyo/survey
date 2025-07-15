<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Rename tabel master_kota_survey menjadi master_kabupaten
        Schema::rename('master_kota_survey', 'master_kabupaten');

        // 2. Ubah kolom kota menjadi nama_kabupaten
        Schema::table('master_kabupaten', function (Blueprint $table) {
            if (Schema::hasColumn('master_kabupaten', 'kota')) {
                $table->renameColumn('kota', 'nama_kabupaten');
            }
        });

        // 3. Tambahkan kolom provinsi_id & foreign key ke master_provinsi
        Schema::table('master_kabupaten', function (Blueprint $table) {
            $table->foreignId('provinsi_id')
                ->nullable()
                ->after('id')
                ->constrained('master_provinsi')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        // 1. Drop foreign key & kolom provinsi_id
        Schema::table('master_kabupaten', function (Blueprint $table) {
            $table->dropConstrainedForeignId('provinsi_id');
        });

        // 2. Rename kolom nama_kabupaten → kota
        Schema::table('master_kabupaten', function (Blueprint $table) {
            if (Schema::hasColumn('master_kabupaten', 'nama_kabupaten')) {
                $table->renameColumn('nama_kabupaten', 'kota');
            }
        });

        // 3. Rename lagi ke nama awal
        Schema::rename('master_kabupaten', 'master_kota_survey');
    }
};
