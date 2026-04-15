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
        Schema::table('plot_hadiah_survey', function (Blueprint $table) {

            // 🔄 Ubah FK periode_survey_id ke master_periode_survey
            if (Schema::hasColumn('plot_hadiah_survey', 'periode_survey_id')) {
                $table->dropForeign(['periode_survey_id']);
                $table->foreign('periode_survey_id')
                    ->references('id')
                    ->on('master_periode_survey')
                    ->nullOnDelete()
                    ->cascadeOnUpdate();
            }

            // 🔄 Ubah nama kolom status_plot -> is_winning
            if (Schema::hasColumn('plot_hadiah_survey', 'status_plot')) {
                $table->renameColumn('status_plot', 'is_winning');
            }

            // 🔁 Pastikan default value-nya 'N'
            if (Schema::hasColumn('plot_hadiah_survey', 'is_winning')) {
                $table->enum('is_winning', ['Y', 'N'])->default('N')->change();
            }

            // 🔁 Ubah default status_respondent_assigned menjadi 'N'
            if (Schema::hasColumn('plot_hadiah_survey', 'status_respondent_assigned')) {
                $table->enum('status_respondent_assigned', ['Y', 'N'])->default('N')->change();
            }

            // ➕ Tambahkan FK ke master_respondent jika belum ada
            if (!Schema::hasColumn('plot_hadiah_survey', 'respondent_id')) {
                $table->foreignId('respondent_id')->nullable()
                    ->after('master_outlet_survey_id')
                    ->constrained('master_respondent')
                    ->nullOnDelete()
                    ->cascadeOnUpdate();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plot_hadiah_survey', function (Blueprint $table) {

            // Hapus kolom respondent_id
            if (Schema::hasColumn('plot_hadiah_survey', 'respondent_id')) {
                $table->dropForeign(['respondent_id']);
                $table->dropColumn('respondent_id');
            }

            // Balik nama kolom is_winning → status_plot
            if (Schema::hasColumn('plot_hadiah_survey', 'is_winning')) {
                $table->renameColumn('is_winning', 'status_plot');
            }

            // Kembalikan default status_plot & status_respondent_assigned
            if (Schema::hasColumn('plot_hadiah_survey', 'status_plot')) {
                $table->enum('status_plot', ['Y', 'N'])->default(null)->change();
            }

            if (Schema::hasColumn('plot_hadiah_survey', 'status_respondent_assigned')) {
                $table->enum('status_respondent_assigned', ['Y', 'N'])->default(null)->change();
            }

            // Kembalikan FK periode_survey_id ke tabel sebelumnya
            if (Schema::hasColumn('plot_hadiah_survey', 'periode_survey_id')) {
                $table->dropForeign(['periode_survey_id']);
                $table->foreign('periode_survey_id')
                    ->references('id')
                    ->on('periode_survey')
                    ->nullOnDelete()
                    ->cascadeOnUpdate();
            }
        });
    }
};
