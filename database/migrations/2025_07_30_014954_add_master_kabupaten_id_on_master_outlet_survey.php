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
            // hanya tambahkan foreign key, jangan buat kolom lagi
            $table->foreign('master_kabupaten_id')
                ->references('id')
                ->on('master_kabupaten')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_outlet_survey', function (Blueprint $table) {
            $table->dropForeign(['master_kabupaten_id']);
        });
    }
};
