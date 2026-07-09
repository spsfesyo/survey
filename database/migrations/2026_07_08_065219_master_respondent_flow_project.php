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
            // 1. Mengubah master_outlet_survey_id menjadi nullable
            $table->unsignedBigInteger('master_outlet_survey_id')->nullable()->change();

            // 2. Menambahkan 3 kolom baru setelah kolom master_outlet_survey_id agar rapi
            $table->string('nama_customer_project')->nullable()->after('master_outlet_survey_id');
            $table->string('nama_toko_project')->nullable()->after('nama_customer_project');
            $table->string('alamat_toko_project')->nullable()->after('nama_toko_project');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_respondent', function (Blueprint $table) {
            // Kembalikan master_outlet_survey_id menjadi NOT NULL jika di-rollback
            $table->unsignedBigInteger('master_outlet_survey_id')->nullable(false)->change();

            // Hapus kolom-kolom baru
            $table->dropColumn([
                'nama_customer_project',
                'nama_toko_project',
                'alamat_toko_project'
            ]);
        });
    }
};
