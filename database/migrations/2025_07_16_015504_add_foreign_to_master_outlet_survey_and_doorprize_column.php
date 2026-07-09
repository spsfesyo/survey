    <?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
        public function up(): void
        {
            // Blok 1: Tambah master_outlet_survey_id jika BELUM ADA
            Schema::table('master_respondent', function (Blueprint $table) {
                if (!Schema::hasColumn('master_respondent', 'master_outlet_survey_id')) {
                    $table->foreignId('master_outlet_survey_id')
                        ->after('id')
                        ->constrained('master_outlet_survey')
                        ->cascadeOnDelete();
                }
            });

            // Blok 2: Drop kolom kota_id HANYA JIKA MASIH ADA
            Schema::table('master_respondent', function (Blueprint $table) {
                if (Schema::hasColumn('master_respondent', 'kota_id')) {
                    // Catatan: Pastikan nama foreign key-nya sesuai standar Laravel (master_respondent_kota_id_foreign)
                    // Jika error saat drop foreign, Anda bisa bungkus dengan try-catch atau jalankan dropColumn saja jika di Navicat tidak pakai FK formal
                    try {
                        $table->dropForeign(['kota_id']);
                    } catch (\Exception $e) {
                        // Abaikan jika foreign key tidak ditemukan atau namanya berbeda
                    }
                    $table->dropColumn('kota_id');
                }
            });

            // Blok 3: Tambah master_kabupaten_id jika BELUM ADA
            Schema::table('master_respondent', function (Blueprint $table) {
                if (!Schema::hasColumn('master_respondent', 'master_kabupaten_id')) {
                    // Pastikan tabel master_kabupaten sudah ada (hasil rename di migrasi sebelumnya)
                    if (Schema::hasTable('master_kabupaten')) {
                        $table->foreignId('master_kabupaten_id')
                            ->after('provinsi_id')
                            ->nullable()
                            ->constrained('master_kabupaten')
                            ->nullOnDelete();
                    }
                }
            });

            // Blok 4: Tambahkan kolom hadiah jika BELUM ADA
            Schema::table('master_respondent', function (Blueprint $table) {
                if (!Schema::hasColumn('master_respondent', 'hadiah')) {
                    $table->string('hadiah')->after('jenis_pertanyaan_id')->nullable();
                }
            });
        }

        public function down(): void
        {
            Schema::table('master_respondent', function (Blueprint $table) {
                // 1. Hapus kolom hadiah
                if (Schema::hasColumn('master_respondent', 'hadiah')) {
                    $table->dropColumn('hadiah');
                }

                // 2. Drop master_kabupaten_id
                if (Schema::hasColumn('master_respondent', 'master_kabupaten_id')) {
                    try {
                        $table->dropForeign(['master_kabupaten_id']);
                    } catch (\Exception $e) {
                    }
                    $table->dropColumn('master_kabupaten_id');
                }

                // 3. Drop master_outlet_survey_id
                if (Schema::hasColumn('master_respondent', 'master_outlet_survey_id')) {
                    try {
                        $table->dropForeign(['master_outlet_survey_id']);
                    } catch (\Exception $e) {
                    }
                    $table->dropColumn('master_outlet_survey_id');
                }
            });
        }
    };
