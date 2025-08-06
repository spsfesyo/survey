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
        Schema::table('master_kabupaten', function (Blueprint $table) {
            $table->foreignId('master_area_id')
                  ->nullable()
                  ->constrained('master_area')
                  ->nullOnDelete()
                  ->after('provinsi_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::dropIfExists('master_kabupaten');
    }
};
