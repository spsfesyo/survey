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
        // Blok 1: Coba hapus unique email secara mandiri
        try {
            Schema::table('master_respondent', function (Blueprint $table) {
                $table->dropUnique(['email_respondent']);
            });
        } catch (\Exception $e) {
            // Diamkan jika index email sudah tidak ada
        }

        // Blok 2: Coba hapus unique telepon secara mandiri
        try {
            Schema::table('master_respondent', function (Blueprint $table) {
                $table->dropUnique(['telepone_respondent']);
            });
        } catch (\Exception $e) {
            // Diamkan jika index telepon sudah tidak ada
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            Schema::table('master_respondent', function (Blueprint $table) {
                $table->unique('email_respondent');
            });
        } catch (\Exception $e) {
        }

        try {
            Schema::table('master_respondent', function (Blueprint $table) {
                $table->unique('telepone_respondent');
            });
        } catch (\Exception $e) {
        }
    }
};
