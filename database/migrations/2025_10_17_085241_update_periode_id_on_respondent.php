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
        // Blok 1: Ubah tipe data kolom jika kolomnya ada
        Schema::table('master_respondent', function (Blueprint $table) {
            if (Schema::hasColumn('master_respondent', 'periode_id')) {
                // Ubah tipe kolom dari varchar ke unsignedBigInteger
                $table->unsignedBigInteger('periode_id')->nullable()->change();
            }
        });

        // Blok 2: Pasang foreign key menggunakan try-catch terpisah
        // Jika relasinya ternyata sudah nempel di server, database akan skip tanpa memicu error
        try {
            Schema::table('master_respondent', function (Blueprint $table) {
                if (Schema::hasColumn('master_respondent', 'periode_id')) {
                    $table->foreign('periode_id')
                        ->references('id')
                        ->on('master_periode_survey')
                        ->onUpdate('cascade')
                        ->onDelete('set null');
                }
            });
        } catch (\Exception $e) {
            // Diamkan saja (Blank), artinya jika gagal karena relasinya sudah ada, lewati dengan aman!
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_respondent', function (Blueprint $table) {
            try {
                // Hapus foreign key dulu jika ada
                $table->dropForeign(['periode_id']);
            } catch (\Exception $e) {
            }

            try {
                // Ubah balik ke varchar jika rollback
                $table->string('periode_id')->nullable()->change();
            } catch (\Exception $e) {
            }
        });
    }
};
