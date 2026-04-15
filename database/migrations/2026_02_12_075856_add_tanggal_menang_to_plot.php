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
            $table->date('tanggal_menang')->nullable()->after('status_respondent_assigned');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plot_hadiah_survey', function (Blueprint $table) {
            $table->dropColumn('tanggal_menang');
        });
    }
};
