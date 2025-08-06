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
        Schema::table('master_outlet_survey', function (Blueprint $table) {
            $table->foreignId('master_area_id')
                  ->nullable()
                  ->constrained('master_area')
                  ->nullOnDelete()
                  ->after('master_kabupaten_id');

                  $table->enum('status_blast_wa', ['true', 'false'])
                        ->default('false')
                        ->after('periode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_outlet_survey');
    }
};
