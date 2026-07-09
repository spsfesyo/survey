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
            // Sederhana: Hanya tambah kolom jika 'tanggal_menang' BELUM ada
            if (!Schema::hasColumn('plot_hadiah_survey', 'tanggal_menang')) {
                $table->date('tanggal_menang')->nullable()->after('status_respondent_assigned');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plot_hadiah_survey', function (Blueprint $table) {
            // Hanya hapus kolom jika memang terdeteksi ada
            if (Schema::hasColumn('plot_hadiah_survey', 'tanggal_menang')) {
                $table->dropColumn('tanggal_menang');
            }
        });
    }
};
