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
          Schema::create('master_hadiah_survey', function (Blueprint $table) {
            $table->id();
            $table->string('kode_hadiah')->nullable();
            $table->string('nama_hadiah')->nullable();
            $table->integer('jumlah_hadiah')->nullable();
            $table->enum('status', ['Y', 'N'])->default('Y');
            $table->timestamps(); // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::dropIfExists('master_hadiah_survey');
    }
};
