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
        Schema::table('master_hadiah_survey', function (Blueprint $table) {
            if (!Schema::hasColumn('master_hadiah_survey', 'periode_survey_id')) {
                $table->foreignId('periode_survey_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('master_periode_survey')
                    ->nullOnDelete()
                    ->cascadeOnUpdate();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_hadiah_survey', function (Blueprint $table) {
            if (Schema::hasColumn('master_hadiah_survey', 'periode_survey_id')) {
                $table->dropForeign(['periode_survey_id']);
                $table->dropColumn('periode_survey_id');
            }
        });
    }
};
