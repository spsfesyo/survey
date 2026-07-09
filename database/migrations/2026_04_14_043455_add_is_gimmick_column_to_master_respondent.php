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
            // Sederhana: Hanya tambah kolom jika 'is_gimmick' BELUM ada
            if (!Schema::hasColumn('master_respondent', 'is_gimmick')) {
                $table->boolean('is_gimmick')->nullable()->after('periode_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_respondent', function (Blueprint $table) {
            // Hanya hapus kolom jika memang ada
            if (Schema::hasColumn('master_respondent', 'is_gimmick')) {
                $table->dropColumn('is_gimmick');
            }
        });
    }
};
