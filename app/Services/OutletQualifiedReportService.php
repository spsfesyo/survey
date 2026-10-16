<?php

namespace App\Services;

use App\Models\MasterPeriode;
use Illuminate\Support\Facades\DB;

class OutletQualifiedReportService
{
    protected OutletQualifiedService $qualifiedService;

    public function __construct(OutletQualifiedService $qualifiedService)
    {
        $this->qualifiedService = $qualifiedService;
    }

    /**
     * Ambil ID master_outlet_survey yang lolos (>=100 m3 + sudah isi survei).
     */
    public function idOutletLolos(int $periodeId, float $minQty = OutletQualifiedService::MIN_QTY_DEFAULT): array
    {
        return $this->qualifiedService->idOutletSurveyLolos($periodeId, $minQty);
    }

    /**
     * Bangun data report (mirip buildReportData di AdminReportController),
     * TAPI dibatasi hanya outlet yang lolos.
     */
    public function buildReportData(int $periodeId, array $outletIdsLolos): array
    {
        if (empty($outletIdsLolos)) {
            return [
                'totalRespondent' => 0,
                'totalBlesscon'   => 0,
                'totalSuperior'   => 0,
                'provinces'       => [],
                'questions'       => [],
            ];
        }

        // Subquery: submission paling awal per responden+pertanyaan
        $earliestBatch = DB::table('answer_survey')
            ->select(
                'master_respondent_id',
                'master_pertanyaan_id',
                DB::raw('MIN(DATE(created_at)) as earliest_date')
            )
            ->groupBy('master_respondent_id', 'master_pertanyaan_id');

        // Ambil data jawaban — dibatasi ke outlet yang lolos
        $rawAnswers = DB::table('answer_survey as a')
            ->join('pertanyaan_options as o', 'a.pertanyaan_options_id', '=', 'o.id')
            ->join('master_respondent as r', 'a.master_respondent_id', '=', 'r.id')
            ->join('master_pertanyaan as p', 'a.master_pertanyaan_id', '=', 'p.id')
            ->leftJoin('master_outlet_survey as os', 'r.master_outlet_survey_id', '=', 'os.id')
            ->leftJoin('master_kabupaten as k', 'k.id', '=', 'os.master_kabupaten_id')   // ⚠️ sesuaikan
            ->leftJoin('master_provinsi as pv', 'pv.id', '=', 'k.provinsi_id')     // ⚠️ sesuaikan
            ->joinSub($earliestBatch, 'eb', function ($join) {
                $join->on('a.master_respondent_id', '=', 'eb.master_respondent_id')
                    ->on('a.master_pertanyaan_id', '=', 'eb.master_pertanyaan_id')
                    ->on(DB::raw('DATE(a.created_at)'), '=', 'eb.earliest_date');
            })
            ->select(
                'p.pertanyaan',
                'o.options as option_text',
                'r.jenis_pertanyaan_id',
                DB::raw('COALESCE(pv.nama_provinsi, "Tidak Diketahui") as nama_provinsi'),
                DB::raw('COUNT(DISTINCT a.master_respondent_id) as total')
            )
            ->where('r.periode_id', $periodeId)
            ->whereIn('r.master_outlet_survey_id', $outletIdsLolos)   // 👈 KUNCI FILTER
            ->groupBy(
                'p.pertanyaan',
                'o.options',
                'r.jenis_pertanyaan_id',
                'nama_provinsi'
            )
            ->get();

        // Daftar semua provinsi (urut abjad) untuk kolom tetap
        $provinces = DB::table('master_provinsi')
            ->orderBy('nama_provinsi')
            ->pluck('nama_provinsi')
            ->toArray();

        // Susun struktur bertingkat
        $questions = [];
        foreach ($rawAnswers as $row) {
            $kategori = $row->jenis_pertanyaan_id == 1 ? 'blesscon' : 'superior';

            if (!isset($questions[$row->pertanyaan][$kategori][$row->option_text])) {
                $questions[$row->pertanyaan][$kategori][$row->option_text] =
                    array_fill_keys($provinces, 0) + ['total' => 0, 'Tidak Diketahui' => 0];
            }

            $questions[$row->pertanyaan][$kategori][$row->option_text][$row->nama_provinsi] = $row->total;
            $questions[$row->pertanyaan][$kategori][$row->option_text]['total'] += $row->total;
        }

        // Total respondent (dibatasi outlet lolos)
        $totalBlesscon = \App\Models\MasterRespondent::where('periode_id', $periodeId)
            ->where('jenis_pertanyaan_id', 1)
            ->whereIn('master_outlet_survey_id', $outletIdsLolos)
            ->count();

        $totalSuperior = \App\Models\MasterRespondent::where('periode_id', $periodeId)
            ->where('jenis_pertanyaan_id', 2)
            ->whereIn('master_outlet_survey_id', $outletIdsLolos)
            ->count();

        return [
            'totalRespondent' => $totalBlesscon + $totalSuperior,
            'totalBlesscon'   => $totalBlesscon,
            'totalSuperior'   => $totalSuperior,
            'provinces'       => $provinces,
            'questions'       => $questions,
        ];
    }

    /**
     * Susun baris detail per outlet (untuk template outlet-qualified).
     * Di sinilah provinsi, kabupaten, area digabung.
     */
    public function susunBaris(int $periodeId, float $minQty = OutletQualifiedService::MIN_QTY_DEFAULT): array
    {
        $hasil = $this->qualifiedService->cari($periodeId, $minQty);

        // Kumpulkan semua ID outlet survey
        $ids = [];
        foreach ($hasil['lolos'] as $sps) {
            foreach ($sps['outlet_survey'] as $o) {
                $ids[] = $o['outlet_survey_id'];
            }
        }

        if (empty($ids)) {
            return [];
        }

        // Ambil detail wilayah sekaligus — hindari N+1
        $detailMap = DB::table('master_outlet_survey as os')
            ->leftJoin('master_kabupaten as k', 'k.id', '=', 'os.master_kabupaten_id')   // ⚠️ sesuaikan nama kolom
            ->leftJoin('master_provinsi as p', 'p.id', '=', 'k.provinsi_id')       // ⚠️ sesuaikan arah join
            ->leftJoin('master_area as a', 'a.id', '=', 'p.master_area_id')                // ⚠️ sesuaikan arah join
            ->whereIn('os.id', $ids)
            ->select(
                'os.id',
                'os.nama_outlet_console',
                'k.nama_kabupaten as kabupaten',
                'p.nama_provinsi as provinsi',
                'a.nama_area as area'
            )
            ->get()
            ->keyBy('id');

        $baris = [];
        foreach ($hasil['lolos'] as $sps) {
            foreach ($sps['outlet_survey'] as $o) {
                $d = $detailMap[$o['outlet_survey_id']] ?? null;

                $baris[] = [
                    'nama_outlet'         => $o['nama_outlet'],
                    'nama_outlet_console' => $d->nama_outlet_console ?? '-',
                    'telepone_outlet'     => $o['telepone_outlet'],
                    'kode_unik'           => $o['kode_unik'],
                    'total_qty'           => $sps['total_qty'],
                    'jumlah_respondent'   => $o['jumlah_respondent'],
                    'provinsi'            => $d->provinsi ?? '-',
                    'kabupaten'           => $d->kabupaten ?? '-',
                    'area'                => $d->area ?? '-',
                ];
            }
        }

        return $baris;
    }
}
