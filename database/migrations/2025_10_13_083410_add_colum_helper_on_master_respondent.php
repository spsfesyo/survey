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
        if (!Schema::hasColumn('master_respondent', 'periode_old')) {
            Schema::table('master_respondent', function (Blueprint $table) {
                // Tambahkan kolom periode_old setelah periode_id lama
                $table->string('periode_old')->nullable()->after('periode_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_respondent', function (Blueprint $table) {
            // Hapus kolom periode_old jika rollback
            if (Schema::hasColumn('master_respondent', 'periode_old')) {
                $table->dropColumn('periode_old');
            }
        });
    }
};
