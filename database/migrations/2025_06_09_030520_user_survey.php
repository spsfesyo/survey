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
        if(!Schema::hasTable('user_survey')) { // untuk mengecek apakah tabel sudah ada sehingga tidak membuat tabel yang sama dan tidak membuat error migrasi

            Schema::create('user_survey', function (Blueprint $table) {
                $table->id(); // ID primary key auto-increment
                $table->string('name'); // kolom nama pengguna
                $table->string('email')->nullable(); // kolom email yang unik, nullable karena bisa kosong
                $table->string('username'); // kolom username yang unik
                $table->string('password'); // kolom password
                $table->unsignedBigInteger('role_id')->nullable(); // kolom role_id yang mengacu pada tabel role_survey
                $table->timestamps();

                $table->foreign('role_id')->references('id')->on('role_survey')->onDelete('cascade'); // foreign key ke tabel role_survey
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_survey'); // jika tabel ada, maka hapus tabel
    }
};
