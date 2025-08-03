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
        if (!Schema::hasTable('history_pemenang_survey')) {
            Schema::create('history_pemenang_survey', function (Blueprint $table) {
                $table->id();

                // FK -> sesuaikan nama tabel target jika berbeda
                $table->foreignId('periode_survey_id')->nullable()
                    ->constrained('periode_survey')   // ganti jika tabelmu beda
                    ->nullOnDelete()
                    ->cascadeOnUpdate();

                $table->foreignId('mater_outlet_survey_id')->nullable()
                    ->constrained('master_outlet_survey')  // ganti jika tabelmu beda
                    ->nullOnDelete()
                    ->cascadeOnUpdate();

                $table->foreignId('master_kabupaten_id')->nullable()
                    ->constrained('master_kabupaten') // sesuai yang sudah kamu pakai
                    ->nullOnDelete()
                    ->cascadeOnUpdate();

                $table->foreignId('hadiah_id')->nullable()
                    ->constrained('master_hadiah_survey')    // ganti jika tabelmu beda
                    ->nullOnDelete()
                    ->cascadeOnUpdate();

                $table->enum('status_history', ['Y', 'N'])
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
        Schema::dropIfExists('history_pemenang_survey');
    }
};
