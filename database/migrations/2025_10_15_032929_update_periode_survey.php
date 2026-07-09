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
        // 1. Hanya RENAME jika tabel lama ADA dan tabel baru BELUM ADA
        if (Schema::hasTable('periode_survey') && !Schema::hasTable('master_periode_survey')) {
            Schema::rename('periode_survey', 'master_periode_survey');
        }

        // 2. Jalankan perubahan struktur HANYA JIKA tabel master_periode_survey sudah siap eksis
        if (Schema::hasTable('master_periode_survey')) {

            Schema::table('master_periode_survey', function (Blueprint $table) {
                // Ubah nama_survey -> nama_periode jika kolom lamanya masih ada
                if (Schema::hasColumn('master_periode_survey', 'nama_survey') && !Schema::hasColumn('master_periode_survey', 'nama_periode')) {
                    $table->renameColumn('nama_survey', 'nama_periode');
                }

                // Ubah start_at -> tanggal_mulai jika kolom lamanya masih ada
                if (Schema::hasColumn('master_periode_survey', 'start_at') && !Schema::hasColumn('master_periode_survey', 'tanggal_mulai')) {
                    $table->renameColumn('start_at', 'tanggal_mulai');
                }

                // Ubah end_at -> tanggal_selesai jika kolom lamanya masih ada
                if (Schema::hasColumn('master_periode_survey', 'end_at') && !Schema::hasColumn('master_periode_survey', 'tanggal_selesai')) {
                    $table->renameColumn('end_at', 'tanggal_selesai');
                }
            });

            // 3. Blok terpisah untuk memodifikasi tipe ENUM status (agar tidak bentrok dengan renameColumn)
            Schema::table('master_periode_survey', function (Blueprint $table) {
                if (Schema::hasColumn('master_periode_survey', 'status')) {
                    $table->enum('status', ['aktif', 'nonaktif'])->default('nonaktif')->change();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('master_periode_survey')) {

            Schema::table('master_periode_survey', function (Blueprint $table) {
                if (Schema::hasColumn('master_periode_survey', 'nama_periode') && !Schema::hasColumn('master_periode_survey', 'nama_survey')) {
                    $table->renameColumn('nama_periode', 'nama_survey');
                }
                if (Schema::hasColumn('master_periode_survey', 'tanggal_mulai') && !Schema::hasColumn('master_periode_survey', 'start_at')) {
                    $table->renameColumn('tanggal_mulai', 'start_at');
                }
                if (Schema::hasColumn('master_periode_survey', 'tanggal_selesai') && !Schema::hasColumn('master_periode_survey', 'end_at')) {
                    $table->renameColumn('tanggal_selesai', 'end_at');
                }
                if (Schema::hasColumn('master_periode_survey', 'status')) {
                    $table->enum('status', ['active', 'inactive'])->default(null)->change();
                }
            });

            if (!Schema::hasTable('periode_survey')) {
                Schema::rename('master_periode_survey', 'periode_survey');
            }
        }
    }
};
