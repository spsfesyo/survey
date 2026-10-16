<?php

// Simpan di: app/Services/OutletQualifiedService.php (buat folder Services kalau belum ada)

namespace App\Services;

use App\Models\MasterPeriode;
use Illuminate\Support\Facades\DB;

class OutletQualifiedService
{
    public const MIN_QTY_DEFAULT = 100;

    /**
     * Hasil utama: toko (SPS) dengan pembelian AAC >= $minQty di SAP selama
     * rentang periode, lalu dicocokkan ke sistem survey.
     * Satuan "toko" = 1 nama SPS (1 SPS bisa punya beberapa baris di master_outlet_survey).
     */
    public function cari(int $periodeId, float $minQty = self::MIN_QTY_DEFAULT): array
    {
        $periode = MasterPeriode::findOrFail($periodeId);

        $petaSql = $this->ambilPetaSql(
            (string) $periode->tanggal_mulai,
            (string) $periode->tanggal_selesai,
            $minQty
        );

        $perSps = $this->cocokkanKeSurvey($petaSql, $periodeId);

        // Sudah isi survei = minimal 1 respondent di periode ini
        $lolos = array_values(array_filter($perSps, fn ($s) => $s['jumlah_respondent'] > 0));
        usort($lolos, fn ($a, $b) => $b['total_qty'] <=> $a['total_qty']);

        $tidakDitemukan = array_values(array_map(
            fn ($k) => $petaSql[$k]['sps_name'],
            array_diff(array_keys($petaSql), array_keys($perSps))
        ));

        return [
            'periode'         => $periode,
            'min_qty'         => $minQty,
            'lolos'           => $lolos,
            'tidak_ditemukan' => $tidakDitemukan,
            'ringkasan'       => [
                'outlet_qty_100_di_sqlserver'      => count($petaSql),
                'dikenali_di_master_outlet_survey' => count($perSps),
                'dikenali_tapi_belum_isi_survei'   => count($perSps) - count($lolos),
                'sudah_isi_survei_dan_qty_100'     => count($lolos),
                'tidak_ditemukan_di_mysql'         => count($tidakDitemukan),
            ],
        ];
    }


    /**
     * ID master_outlet_survey dari SEMUA baris milik toko yang lolos.
     * Dipakai untuk membatasi laporan rekap jawaban ke toko >= 100 m3.
     */
    public function idOutletSurveyLolos(int $periodeId, float $minQty = self::MIN_QTY_DEFAULT): array
    {
        $ids = [];

        foreach ($this->cari($periodeId, $minQty)['lolos'] as $sps) {
            foreach ($sps['outlet_survey'] as $baris) {
                $ids[] = $baris['outlet_survey_id'];
            }
        }

        return $ids;
    }


    /**
     * SQL Server: total qty AAC per SPS. Hasil: [kunciNama => ['sps_name', 'qty']]
     */
    private function ambilPetaSql(string $mulai, string $selesai, float $minQty): array
    {
        $hasil = DB::connection('sqlsrv')->select("
            SELECT
                m.SPSName,
                SUM(TRY_CAST(d.Quantity AS FLOAT)) AS TotalQty
            FROM MARKETING..ODLN o
            LEFT JOIN MARKETING..DLN1 d ON o.DocEntry = d.DocEntry
            LEFT JOIN DBMOLIVE..M_OutletConsolidation m ON m.OutletCode = o.U_VOutlet
            WHERE o.CANCELED = 'N'
              AND d.U_V_VERIANTITEM = 'AAC'
              AND o.DocDate >= CONVERT(DATE, ?, 23)
              AND o.DocDate < DATEADD(DAY, 1, CONVERT(DATE, ?, 23))
              AND m.SPSName IS NOT NULL
            GROUP BY m.SPSName
            HAVING SUM(TRY_CAST(d.Quantity AS FLOAT)) >= ?
            ORDER BY TotalQty DESC
        ", [$mulai, $selesai, $minQty]);

        $peta = [];

        foreach ($hasil as $row) {
            $key = $this->kunciNamaOutlet($row->SPSName);

            if ($key === '' || $key === '-') {
                continue; // buang placeholder "-" (outlet belum ter-mapping)
            }

            $peta[$key] = [
                'sps_name' => $row->SPSName,
                'qty'      => round((float) $row->TotalQty, 2),
            ];
        }

        return $peta;
    }


    /**
     * MySQL: cocokkan nama SPS ke master_outlet_survey (whereIn, sama seperti
     * alur masuk survey), hitung respondent tiap baris di periode terpilih,
     * lalu gabungkan per SPS.
     */
    private function cocokkanKeSurvey(array $petaSql, int $periodeId): array
    {
        $namaList = array_column($petaSql, 'sps_name');
        $baris    = collect();

        foreach (array_chunk($namaList, 500) as $chunk) {
            $rows = DB::table('master_outlet_survey as os')
                ->leftJoin('master_respondent as r', function ($join) use ($periodeId) {
                    $join->on('r.master_outlet_survey_id', '=', 'os.id')
                        ->where('r.periode_id', '=', $periodeId);
                })
                ->whereIn('os.sps_internal_name', $chunk)
                ->select(
                    'os.id',
                    'os.nama_outlet',
                    'os.sps_internal_name',
                    'os.kode_unik',
                    'os.status_kode_unik',
                    'os.telepone_outlet',
                    DB::raw('COUNT(r.id) as jumlah_respondent')
                )
                ->groupBy(
                    'os.id',
                    'os.nama_outlet',
                    'os.sps_internal_name',
                    'os.kode_unik',
                    'os.status_kode_unik',
                    'os.telepone_outlet'
                )
                ->get();

            $baris = $baris->concat($rows);
        }

        $perSps = [];

        foreach ($baris as $o) {
            $key = $this->kunciNamaOutlet($o->sps_internal_name);

            if (!isset($petaSql[$key])) {
                continue;
            }

            if (!isset($perSps[$key])) {
                $perSps[$key] = [
                    'sps_name'                   => $petaSql[$key]['sps_name'],
                    'total_qty'                  => $petaSql[$key]['qty'],
                    'jumlah_baris_outlet_survey' => 0,
                    'jumlah_respondent'          => 0,
                    'outlet_survey'              => [],
                ];
            }

            $perSps[$key]['jumlah_baris_outlet_survey']++;
            $perSps[$key]['jumlah_respondent'] += (int) $o->jumlah_respondent;
            $perSps[$key]['outlet_survey'][] = [
                'outlet_survey_id'  => $o->id,
                'nama_outlet'       => $o->nama_outlet,
                'kode_unik'         => $o->kode_unik,
                'status_kode_unik'  => $o->status_kode_unik,
                'telepone_outlet'   => $o->telepone_outlet,
                'jumlah_respondent' => (int) $o->jumlah_respondent,
            ];
        }

        return $perSps;
    }


    /**
     * Kunci pembanding nama: trim + huruf kecil, sama perilakunya dengan
     * where('sps_internal_name', ...) di alur masuk survey.
     */
    private function kunciNamaOutlet(?string $nama): string
    {
        return mb_strtolower(trim((string) $nama));
    }
}
