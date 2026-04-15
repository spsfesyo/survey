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
        if (Schema::hasTable('periode_survey')) {
            Schema::rename('periode_survey', 'master_periode_survey');
        }

        // Update structure
        Schema::table('master_periode_survey', function (Blueprint $table) {
            $table->renameColumn('nama_survey', 'nama_periode');
            $table->renameColumn('start_at', 'tanggal_mulai');
            $table->renameColumn('end_at', 'tanggal_selesai');

            $table->enum('status', ['aktif', 'nonaktif'])->default('nonaktif')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('master_periode_survey', 'periode_survey');

        Schema::table('periode_survey', function (Blueprint $table) {
            $table->renameColumn('nama_periode', 'nama_survey');
            $table->renameColumn('tanggal_mulai', 'start_at');
            $table->renameColumn('tanggal_selesai', 'end_at');
            $table->enum('status', ['active', 'inactive'])->default(null)->change();
        });
    }
};
