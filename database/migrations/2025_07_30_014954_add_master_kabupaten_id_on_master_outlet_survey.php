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
        try {
            Schema::table('master_outlet_survey', function (Blueprint $table) {
                $table->foreign('master_kabupaten_id')
                    ->references('id')
                    ->on('master_kabupaten')
                    ->nullOnDelete()
                    ->cascadeOnUpdate();
            });
        } catch (\Exception $e) {
            // Diamkan saja (Blank), artinya jika gagal karena sudah ada relasinya, lewati dengan aman!
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            Schema::table('master_outlet_survey', function (Blueprint $table) {
                $table->dropForeign(['master_kabupaten_id']);
            });
        } catch (\Exception $e) {
            // Diamkan juga saat rollback jika relasi memang sudah tidak ada
        }
    }
};
