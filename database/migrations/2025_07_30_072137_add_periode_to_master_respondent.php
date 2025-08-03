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
            //

            $table->foreignId('periode_survey_id')
                ->nullable()
                ->after('id')
                ->constrained('periode_survey') // ganti dengan nama tabel periode survey kamu
                ->nullOnDelete()                 // set null ketika periode dihapus
                ->cascadeOnUpdate();             // update otomatis ketika id periode berubah

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_respondent', function (Blueprint $table) {
            //
             $table->dropForeign(['periode_survey_id']);
            $table->dropColumn('periode_survey_id');
        });
    }
};
