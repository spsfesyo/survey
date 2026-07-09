<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        // 1. Amankan proses rename tabel
        // Hanya rename jika tabel lama MASIH ADA, dan tabel baru BELUM ADA
        if (Schema::hasTable('master_kota_survey') && !Schema::hasTable('master_kabupaten')) {
            Schema::rename('master_kota_survey', 'master_kabupaten');
        }

        // Jalankan perintah di bawah ini hanya jika tabel 'master_kabupaten' sudah eksis (baik hasil rename atau hasil inject Navicat)
        if (Schema::hasTable('master_kabupaten')) {

            // 2. Ubah nama kolom kota menjadi nama_kabupaten jika masih bernama 'kota'
            Schema::table('master_kabupaten', function (Blueprint $table) {
                if (Schema::hasColumn('master_kabupaten', 'kota') && !Schema::hasColumn('master_kabupaten', 'nama_kabupaten')) {
                    $table->renameColumn('kota', 'nama_kabupaten');
                }
            });

            // 3. Tambahkan kolom provinsi_id jika BELUM ada
            Schema::table('master_kabupaten', function (Blueprint $table) {
                if (!Schema::hasColumn('master_kabupaten', 'provinsi_id')) {
                    $table->foreignId('provinsi_id')
                        ->nullable()
                        ->after('id')
                        ->constrained('master_provinsi')
                        ->cascadeOnDelete();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('master_kabupaten')) {
            // 1. Drop foreign key & kolom provinsi_id jika ada
            Schema::table('master_kabupaten', function (Blueprint $table) {
                if (Schema::hasColumn('master_kabupaten', 'provinsi_id')) {
                    $table->dropConstrainedForeignId('provinsi_id');
                }
            });

            // 2. Rename kolom nama_kabupaten → kota jika ada
            Schema::table('master_kabupaten', function (Blueprint $table) {
                if (Schema::hasColumn('master_kabupaten', 'nama_kabupaten') && !Schema::hasColumn('master_kabupaten', 'kota')) {
                    $table->renameColumn('nama_kabupaten', 'kota');
                }
            });
        }

        // 3. Rename balik ke nama awal jika tabel kota_survey belum ada
        if (Schema::hasTable('master_kabupaten') && !Schema::hasTable('master_kota_survey')) {
            Schema::rename('master_kabupaten', 'master_kota_survey');
        }
    }
};
