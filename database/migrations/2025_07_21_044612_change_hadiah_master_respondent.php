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
             Schema::table('master_respondent', function (Blueprint $table) {
            if (!Schema::hasColumn('master_respondent', 'master_outlet_survey_id')) {
                $table->foreignId('master_outlet_survey_id')->nullable()
                      ->constrained('master_outlet_survey')
                      ->cascadeOnDelete()->after('id');
            }

            if (!Schema::hasColumn('master_respondent', 'provinsi_id')) {
                $table->foreignId('provinsi_id')->nullable()
                      ->constrained('master_provinsi')
                      ->cascadeOnDelete()->after('master_outlet_survey_id');
            }

            if (!Schema::hasColumn('master_respondent', 'master_kabupaten_id')) {
                $table->foreignId('master_kabupaten_id')->nullable()
                      ->constrained('master_kabupaten')
                      ->cascadeOnDelete()->after('provinsi_id');
            }

            if (!Schema::hasColumn('master_respondent', 'hadiah_id')) {
                $table->foreignId('hadiah_id')->nullable()
                      ->constrained('master_hadiah_survey')
                      ->cascadeOnDelete()->after('master_kabupaten_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_respondent', function (Blueprint $table) {
            // Hapus foreign key dan kolom hadiah_id
            if (Schema::hasColumn('master_respondent', 'hadiah_id')) {
                $table->dropForeign(['hadiah_id']);
                $table->dropColumn('hadiah_id');
            }

            // Tambahkan kembali kolom hadiah (string)
            $table->string('hadiah')->after('jenis_pertanyaan_id')->nullable();
        });
    }
};
