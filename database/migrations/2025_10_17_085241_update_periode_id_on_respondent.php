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
            // Pastikan kolomnya memang sudah ada
            if (Schema::hasColumn('master_respondent', 'periode_id')) {
                // Ubah tipe kolom dari varchar ke unsignedBigInteger
                $table->unsignedBigInteger('periode_id')->nullable()->change();

                // Tambahkan relasi foreign key
                $table->foreign('periode_id')
                    ->references('id')
                    ->on('master_periode_survey')
                    ->onUpdate('cascade')
                    ->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_respondent', function (Blueprint $table) {
            // Hapus foreign key dulu
            $table->dropForeign(['periode_id']);

            // Ubah balik ke varchar jika rollback
            $table->string('periode_id')->nullable()->change();
        });
    }
};
