<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('master_pertanyaan', function (Blueprint $table) {
            // Tambahkan kolom reference jika belum ada
            if (!Schema::hasColumn('master_pertanyaan', 'reference')) {
                $table->text('reference')->nullable()->after('order');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_pertanyaan', function (Blueprint $table) {
            $table->dropColumn('reference');
        });
    }
};