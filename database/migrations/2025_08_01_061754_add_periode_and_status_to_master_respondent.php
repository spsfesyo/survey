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
            $table->string('periode_id', 7)->after('hadiah_id')->nullable(); // untuk menyimpan '2024-08', dll

            $table->enum('status_hadiah', ['ACTIVE', 'INACTIVE'])->after('periode_id')
                ->nullable()
                ->default(null);

          
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_respondent');
    }
};
