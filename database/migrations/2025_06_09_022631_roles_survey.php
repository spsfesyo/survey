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
        if (!Schema::hasTable('role_survey')) { // untuk mengecek apakah tabel sudah ada sehingga tidak membuat tabel yang sama dan tidak membuat error migrasi

            Schema::create('role_survey', function (Blueprint $table) {
                $table->id(); // ID primary key auto-increment
                $table->string('role_name'); // kolom nama peran yang unik
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_survey');
    }
};
