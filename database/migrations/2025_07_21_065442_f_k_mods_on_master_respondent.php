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
            if (! Schema::hasColumn('master_respondent', 'master_outlet_survey_id')) {
                $table->foreignId('master_outlet_survey_id')
                      ->nullable()
                      ->constrained('master_outlet_survey')
                      ->nullOnDelete()
                      ->after('id');
            }

            if (! Schema::hasColumn('master_respondent', 'provinsi_id')) {
                $table->foreignId('provinsi_id')
                      ->nullable()
                      ->constrained('master_provinsi')
                      ->nullOnDelete()
                      ->after('master_outlet_survey_id');
            }

            if (! Schema::hasColumn('master_respondent', 'master_kabupaten_id')) {
                $table->foreignId('master_kabupaten_id')
                      ->nullable()
                      ->constrained('master_kabupaten')
                      ->nullOnDelete()
                      ->after('provinsi_id');
            }

            if (! Schema::hasColumn('master_respondent', 'hadiah_id')) {
                $table->foreignId('hadiah_id')
                      ->nullable()
                      ->constrained('master_hadiah_survey')
                      ->nullOnDelete()
                      ->after('master_kabupaten_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('master_respondent', function (Blueprint $table) {
            if (Schema::hasColumn('master_respondent', 'hadiah_id')) {
                $table->dropConstrainedForeignId('hadiah_id');
            }
            if (Schema::hasColumn('master_respondent', 'master_kabupaten_id')) {
                $table->dropConstrainedForeignId('master_kabupaten_id');
            }
            if (Schema::hasColumn('master_respondent', 'provinsi_id')) {
                $table->dropConstrainedForeignId('provinsi_id');
            }
            if (Schema::hasColumn('master_respondent', 'master_outlet_survey_id')) {
                $table->dropConstrainedForeignId('master_outlet_survey_id');
            }
        });
    }
};
