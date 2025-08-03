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
        if (!Schema::hasTable('aturan_hadiah_survey')) {
            Schema::create('aturan_hadiah_survey', function (Blueprint $table) {
                $table->id();

                // FK -> sesuaikan nama tabel target jika berbeda
                $table->foreignId('periode_survey_id')->nullable()
                    ->constrained('periode_survey')   // ganti jika tabelmu beda
                    ->nullOnDelete()
                    ->cascadeOnUpdate();

                $table->foreignId('provinsi_id')->nullable()
                    ->constrained('master_provinsi')  // ganti jika tabelmu beda
                    ->nullOnDelete()
                    ->cascadeOnUpdate();

                $table->foreignId('master_kabupaten_id')->nullable()
                    ->constrained('master_kabupaten') // sesuai yang sudah kamu pakai
                    ->nullOnDelete()
                    ->cascadeOnUpdate();

                $table->integer('slot_hadiah_kota')->nullable();

                $table->foreignId('hadiah_id')->nullable()
                    ->constrained('master_hadiah_survey')    // ganti jika tabelmu beda
                    ->nullOnDelete()
                    ->cascadeOnUpdate();

                $table->enum('status_aturan', ['Y', 'N'])
                    ->nullable()
                    ->default(null);

                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aturan_hadiah_survey');
    }
};
