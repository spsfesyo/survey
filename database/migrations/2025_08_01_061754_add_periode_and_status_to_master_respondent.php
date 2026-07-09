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
            if (!Schema::hasColumn('master_respondent', 'periode_id')) {
                $table->string('periode_id', 7)->after('hadiah_id')->nullable();
            }
        });

        // Blok 2: Tambah status_hadiah jika belum ada
        Schema::table('master_respondent', function (Blueprint $table) {
            if (!Schema::hasColumn('master_respondent', 'status_hadiah')) {
                $table->enum('status_hadiah', ['ACTIVE', 'INACTIVE'])->after('periode_id')
                    ->nullable()
                    ->default(null);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_respondent', function (Blueprint $table) {
            // AMAN: Hanya hapus kolom yang kita tambahkan saja, bukan hapus seluruh tabel!
            if (Schema::hasColumn('master_respondent', 'status_hadiah')) {
                $table->dropColumn('status_hadiah');
            }

            if (Schema::hasColumn('master_respondent', 'periode_id')) {
                $table->dropColumn('periode_id');
            }
        });
    }
};
