<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('master_respondent', function (Blueprint $table) {
            // 1. Tambah foreign key ke master_outlet_survey setelah id
            $table->foreignId('master_outlet_survey_id')
                  ->after('id')
                  ->constrained('master_outlet_survey')
                  ->cascadeOnDelete();

            // 2. Drop kolom kota_id jika ada
            if (Schema::hasColumn('master_respondent', 'kota_id')) {
                $table->dropForeign(['kota_id']);
                $table->dropColumn('kota_id');
            }

            // 3. Tambah kolom master_kabupaten_id sebagai pengganti kota_id
            $table->foreignId('master_kabupaten_id')
                  ->after('provinsi_id')
                  ->nullable()
                  ->constrained('master_kabupaten')
                  ->nullOnDelete();

            // 4. Tambahkan kolom hadiah setelah jenis_pertanyaan_id
            $table->string('hadiah')->after('jenis_pertanyaan_id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('master_respondent', function (Blueprint $table) {
            // 1. Hapus kolom hadiah
            if (Schema::hasColumn('master_respondent', 'hadiah')) {
                $table->dropColumn('hadiah');
            }

            // 2. Drop master_kabupaten_id
            if (Schema::hasColumn('master_respondent', 'master_kabupaten_id')) {
                $table->dropForeign(['master_kabupaten_id']);
                $table->dropColumn('master_kabupaten_id');
            }

            // 3. Kembalikan kota_id jika diperlukan
            // (opsional, boleh diabaikan jika tidak perlu)
            // $table->foreignId('kota_id')->after('provinsi_id')->constrained('master_kota_survey');

            // 4. Drop master_outlet_survey_id
            if (Schema::hasColumn('master_respondent', 'master_outlet_survey_id')) {
                $table->dropForeign(['master_outlet_survey_id']);
                $table->dropColumn('master_outlet_survey_id');
            }
        });
    }
};
