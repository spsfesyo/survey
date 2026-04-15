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
        Schema::table('plot_hadiah_survey', function (Blueprint $table) {
            if (!Schema::hasColumn('plot_hadiah_survey', 'master_area_id')) {
                $table->foreignId('master_area_id')
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
        Schema::table('plot_hadiah_survey', function (Blueprint $table) {
            if (Schema::hasColumn('plot_hadiah_survey', 'master_area_id')) {
                $table->dropForeign(['master_area_id']);
                $table->dropColumn('master_area_id');
            }
        });
    }
};
