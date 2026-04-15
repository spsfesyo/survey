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
            if (!Schema::hasColumn('master_respondent', 'area_id')) {
                $table->foreignId('area_id')
                    ->nullable()
                    ->after('master_kabupaten_id')
                    ->constrained('master_area')
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
        Schema::table('master_respondent', function (Blueprint $table) {
            if (Schema::hasColumn('master_respondent', 'area_id')) {
                $table->dropForeign(['area_id']);
                $table->dropColumn('area_id');
            }
        });
    }
};
