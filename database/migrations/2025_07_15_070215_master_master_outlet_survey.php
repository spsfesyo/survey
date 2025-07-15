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
        if (!Schema::hasTable('master_outlet_survey')) {
            Schema::create('master_outlet_survey', function (Blueprint $table) {
                $table->id();
                $table->string('nama_outlet')->nullable();
                $table->string('sps_internal_name')->nullable();
                $table->string('telepone_outlet')->nullable();
                $table->string('kode_unik', 10)->unique()->nullable();
                $table->enum('status_kode_unik', ['Y', 'N'])
                    ->default('Y');


                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
