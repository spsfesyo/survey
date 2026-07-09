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

        Schema::table('master_outlet_survey', function (Blueprint $table) {
            // Sederhana: Hanya tambah kolom jika kolom 'periode' BELUM ada
            if (!Schema::hasColumn('master_outlet_survey', 'periode')) {
                $table->string('periode', 7)->after('status_kode_unik')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_outlet_survey', function (Blueprint $table) {
            // AMAN: Hanya hapus kolom 'periode'-nya saja, bukan hapus satu tabel!
            if (Schema::hasColumn('master_outlet_survey', 'periode')) {
                $table->dropColumn('periode');
            }
        });
    }
};
