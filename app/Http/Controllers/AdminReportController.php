<?php

namespace App\Http\Controllers;

use App\Models\MasterPeriode;
use App\Models\MasterRespondent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Facades\Excel;

class AdminReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    // public function index(Request $request)
    // {
    //     $query = MasterPeriode::query();

    //     // panggil function search
    //     $query = $this->searchByDate($query, $request);

    //     $periode = $query->paginate(1)->withQueryString();

    //     return view('Admin.Report.admin-report', compact('periode'));
    // }


    // private function searchByDate($query, $request)
    // {
    //     if ($request->filled('tanggal')) {

    //         $tanggal = $request->tanggal;

    //         $query->whereDate('tanggal_mulai', '<=', $tanggal)
    //             ->whereDate('tanggal_selesai', '>=', $tanggal);
    //     }

    //     return $query;
    // }


    // public function earliestBatchSubquery()
    // {
    //     return DB::table('answer_survey')
    //         ->select(
    //             'master_respondent_id',
    //             'master_pertanyaan_id',
    //             DB::raw('MIN(DATE(created_at)) as earliest_date')
    //         )
    //         ->groupBy('master_respondent_id', 'master_pertanyaan_id');
    // }


    // public function export($id)
    // {
    //     $periode = MasterPeriode::findOrFail($id);

    //     return Excel::download(

    //         new class($periode->id) implements FromView {

    //             protected $periodeId;

    //             public function __construct($periodeId)
    //             {
    //                 $this->periodeId = $periodeId;
    //             }

    //             public function view(): \Illuminate\Contracts\View\View
    //             {
    //                 // 🔹 Subquery: cari tanggal submission PALING AWAL untuk tiap
    //                 // kombinasi responden + pertanyaan. Ini dipakai untuk membuang
    //                 // baris jawaban dari submission duplikat yang muncul belakangan
    //                 // (misalnya kasus ~149 responden yang seluruh jawabannya
    //                 // ter-duplikat dengan created_at ~1 tahun setelah submission asli).
    //                 $earliestBatch = DB::table('answer_survey')
    //                     ->select(
    //                         'master_respondent_id',
    //                         'master_pertanyaan_id',
    //                         DB::raw('MIN(DATE(created_at)) as earliest_date')
    //                     )
    //                     ->groupBy('master_respondent_id', 'master_pertanyaan_id');

    //                 // 🔹 ambil data
    //                 // Catatan perbaikan:
    //                 // 1. COUNT(DISTINCT a.master_respondent_id) -> hilangkan efek baris
    //                 //    duplikat (1 responden, 1 opsi yang sama, tersimpan >1x).
    //                 // 2. joinSub ke $earliestBatch -> hanya ambil jawaban dari tanggal
    //                 //    submission PALING AWAL, buang baris dari submission duplikat
    //                 //    yang muncul belakangan (baik yang opsinya sama maupun beda).
    //                 $answers = DB::table('answer_survey as a')
    //                     ->join('pertanyaan_options as o', 'a.pertanyaan_options_id', '=', 'o.id')
    //                     ->join('master_respondent as r', 'a.master_respondent_id', '=', 'r.id')
    //                     ->join('master_pertanyaan as p', 'a.master_pertanyaan_id', '=', 'p.id')
    //                     ->joinSub($earliestBatch, 'eb', function ($join) {
    //                         $join->on('a.master_respondent_id', '=', 'eb.master_respondent_id')
    //                             ->on('a.master_pertanyaan_id', '=', 'eb.master_pertanyaan_id')
    //                             ->on(DB::raw('DATE(a.created_at)'), '=', 'eb.earliest_date');
    //                     })
    //                     ->select(
    //                         'p.pertanyaan',
    //                         'o.options as option_text',
    //                         'r.jenis_pertanyaan_id',
    //                         DB::raw('COUNT(DISTINCT a.master_respondent_id) as total')
    //                     )
    //                     ->where('r.periode_id', $this->periodeId)
    //                     ->whereNotNull('r.master_outlet_survey_id') // hanya yang punya outlet ID
    //                     ->groupBy(
    //                         'p.pertanyaan',
    //                         'o.options',
    //                         'r.jenis_pertanyaan_id'
    //                     )
    //                     ->get();

    //                 // 🔹 total respondent
    //                 $totalBlesscon = \App\Models\MasterRespondent::where('periode_id', $this->periodeId)
    //                     ->where('jenis_pertanyaan_id', 1)
    //                     ->whereNotNull('master_outlet_survey_id') // hanya yang punya outlet ID
    //                     ->count();

    //                 $totalSuperior = \App\Models\MasterRespondent::where('periode_id', $this->periodeId)
    //                     ->where('jenis_pertanyaan_id', 2)
    //                     ->whereNotNull('master_outlet_survey_id') // hanya yang punya outlet ID
    //                     ->count();

    //                 $totalRespondent = $totalBlesscon + $totalSuperior;

    //                 return view('Admin.Report.template-report', [
    //                     'answers' => $answers,
    //                     'totalBlesscon' => $totalBlesscon,
    //                     'totalSuperior' => $totalSuperior,
    //                     'totalRespondent' => $totalRespondent
    //                 ]);
    //             }
    //         },

    //         'report-survey-' . $periode->id . '.xlsx'
    //     );
    // }
    // /**
    //  * UNTUK FITUR DI PROJECT
    //  */
    // public function indexProject(Request $request)
    // {
    //     $query = MasterPeriode::query();

    //     // panggil function search
    //     $query = $this->searchByDate($query, $request);

    //     $periode = $query->paginate(1)->withQueryString();

    //     return view('Admin.Report.admin-report-project', compact('periode'));
    // }

    // public function exportProject($id)
    // {
    //     $periode = MasterPeriode::findOrFail($id);

    //     return Excel::download(

    //         new class($periode->id, $this) implements FromView {

    //             protected $periodeId;
    //             protected $controller;

    //             public function __construct($periodeId, $controller)
    //             {
    //                 $this->periodeId = $periodeId;
    //                 $this->controller = $controller;
    //             }

    //             public function view(): \Illuminate\Contracts\View\View
    //             {
    //                 $earliestBatch = $this->controller->earliestBatchSubquery();

    //                 // 🔹 Ambil data jawaban (Khusus Project: master_outlet_survey_id IS NULL)
    //                 // Fix yang sama dengan export() outlet: COUNT(DISTINCT) + filter
    //                 // submission paling awal, cuma beda di whereNull vs whereNotNull.
    //                 $answers = DB::table('answer_survey as a')
    //                     ->join('pertanyaan_options as o', 'a.pertanyaan_options_id', '=', 'o.id')
    //                     ->join('master_respondent as r', 'a.master_respondent_id', '=', 'r.id')
    //                     ->join('master_pertanyaan as p', 'a.master_pertanyaan_id', '=', 'p.id')
    //                     ->joinSub($earliestBatch, 'eb', function ($join) {
    //                         $join->on('a.master_respondent_id', '=', 'eb.master_respondent_id')
    //                             ->on('a.master_pertanyaan_id', '=', 'eb.master_pertanyaan_id')
    //                             ->on(DB::raw('DATE(a.created_at)'), '=', 'eb.earliest_date');
    //                     })
    //                     ->select(
    //                         'p.pertanyaan',
    //                         'o.options as option_text',
    //                         'r.jenis_pertanyaan_id',
    //                         DB::raw('COUNT(DISTINCT a.master_respondent_id) as total')
    //                     )
    //                     ->where('r.periode_id', $this->periodeId)
    //                     ->whereNull('r.master_outlet_survey_id') // 👈 Mengambil data yang NULL (Project)
    //                     ->groupBy(
    //                         'p.pertanyaan',
    //                         'o.options',
    //                         'r.jenis_pertanyaan_id'
    //                     )
    //                     ->get();

    //                 // 🔹 Total respondent Blesscon (Project)
    //                 $totalBlesscon = \App\Models\MasterRespondent::where('periode_id', $this->periodeId)
    //                     ->where('jenis_pertanyaan_id', 1)
    //                     ->whereNull('master_outlet_survey_id') // 👈 Diubah ke whereNull
    //                     ->count();

    //                 // 🔹 Total respondent Superior (Project)
    //                 $totalSuperior = \App\Models\MasterRespondent::where('periode_id', $this->periodeId)
    //                     ->where('jenis_pertanyaan_id', 2)
    //                     ->whereNull('master_outlet_survey_id') // 👈 Diubah ke whereNull
    //                     ->count();

    //                 $totalRespondent = $totalBlesscon + $totalSuperior;

    //                 return view('Admin.Report.template-report', [
    //                     'answers' => $answers,
    //                     'totalBlesscon' => $totalBlesscon,
    //                     'totalSuperior' => $totalSuperior,
    //                     'totalRespondent' => $totalRespondent
    //                 ]);
    //             }
    //         },

    //         'report-survey-project-' . $periode->id . '.xlsx'
    //     );
    // }




    public function index(Request $request)
    {
        $query = MasterPeriode::query();
        $query = $this->searchByDate($query, $request);
        $periode = $query->paginate(10)->withQueryString();

        return view('Admin.GeneralReport.admin-report', compact('periode'));
    }

    private function searchByDate($query, $request)
    {
        if ($request->filled('tanggal')) {
            $tanggal = $request->tanggal;
            $query->whereDate('tanggal_mulai', '<=', $tanggal)
                ->whereDate('tanggal_selesai', '>=', $tanggal);
        }
        return $query;
    }

    /**
     * Subquery tanggal submission PALING AWAL per kombinasi responden+pertanyaan,
     * untuk membuang baris jawaban dari submission duplikat yang muncul belakangan.
     */
    public function earliestBatchSubquery()
    {
        return DB::table('answer_survey')
            ->select(
                'master_respondent_id',
                'master_pertanyaan_id',
                DB::raw('MIN(DATE(created_at)) as earliest_date')
            )
            ->groupBy('master_respondent_id', 'master_pertanyaan_id');
    }

    /**
     * Ambil & susun data laporan (dipakai bareng oleh export() dan exportProject()).
     * $outletMode: true = whereNotNull master_outlet_survey_id (laporan Outlet)
     *              false = whereNull master_outlet_survey_id (laporan Project)
     *
     * Return array siap pakai di Blade:
     * [
     *   'totalRespondent' => int,
     *   'totalBlesscon'   => int,
     *   'totalSuperior'   => int,
     *   'provinces'       => ['Jawa Timur', 'Jawa Barat', ...]  (urut abjad, semua provinsi)
     *   'questions'       => [
     *        'Teks Pertanyaan' => [
     *            'blesscon' => [
     *                'Opsi A' => ['Jawa Timur' => 2, 'Jawa Barat' => 0, ..., 'total' => 2],
     *                ...
     *            ],
     *            'superior' => [ ... sama strukturnya ... ],
     *        ],
     *        ...
     *   ]
     * ]
     */
    private function buildReportData(int $periodeId, bool $outletMode): array
    {
        $earliestBatch = $this->earliestBatchSubquery();

        $query = DB::table('answer_survey as a')
            ->join('pertanyaan_options as o', 'a.pertanyaan_options_id', '=', 'o.id')
            ->join('master_respondent as r', 'a.master_respondent_id', '=', 'r.id')
            ->join('master_pertanyaan as p', 'a.master_pertanyaan_id', '=', 'p.id')
            ->leftJoin('master_provinsi as pv', 'r.provinsi_id', '=', 'pv.id')
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
            ->groupBy(
                'p.pertanyaan',
                'o.options',
                'r.jenis_pertanyaan_id',
                'nama_provinsi'
            );

        if ($outletMode) {
            $query->whereNotNull('r.master_outlet_survey_id');
        } else {
            $query->whereNull('r.master_outlet_survey_id');
        }

        $rawAnswers = $query->get();

        // 🔹 Daftar SEMUA provinsi dari master_provinsi (urut abjad), dipakai sebagai
        // kolom tetap supaya provinsi tanpa data pun tetap tampil dengan angka 0.
        $provinces = DB::table('master_provinsi')
            ->orderBy('nama_provinsi')
            ->pluck('nama_provinsi')
            ->toArray();

        // 🔹 Susun struktur bertingkat: pertanyaan -> kategori -> opsi -> provinsi -> total
        $questions = [];

        foreach ($rawAnswers as $row) {
            $kategori = $row->jenis_pertanyaan_id == 1 ? 'blesscon' : 'superior';

            // inisialisasi semua provinsi dengan 0 dulu, supaya provinsi tanpa
            // data tetap muncul di baris ini
            if (!isset($questions[$row->pertanyaan][$kategori][$row->option_text])) {
                $questions[$row->pertanyaan][$kategori][$row->option_text] =
                    array_fill_keys($provinces, 0) + ['total' => 0, 'Tidak Diketahui' => 0];
            }

            $questions[$row->pertanyaan][$kategori][$row->option_text][$row->nama_provinsi] = $row->total;
            $questions[$row->pertanyaan][$kategori][$row->option_text]['total'] += $row->total;
        }

        $totalBlesconQuery = \App\Models\MasterRespondent::where('periode_id', $periodeId)
            ->where('jenis_pertanyaan_id', 1);
        $totalSuperiorQuery = \App\Models\MasterRespondent::where('periode_id', $periodeId)
            ->where('jenis_pertanyaan_id', 2);

        if ($outletMode) {
            $totalBlesconQuery->whereNotNull('master_outlet_survey_id');
            $totalSuperiorQuery->whereNotNull('master_outlet_survey_id');
        } else {
            $totalBlesconQuery->whereNull('master_outlet_survey_id');
            $totalSuperiorQuery->whereNull('master_outlet_survey_id');
        }

        $totalBlesscon = $totalBlesconQuery->count();
        $totalSuperior = $totalSuperiorQuery->count();

        return [
            'totalRespondent' => $totalBlesscon + $totalSuperior,
            'totalBlesscon'   => $totalBlesscon,
            'totalSuperior'   => $totalSuperior,
            'provinces'       => $provinces,
            'questions'       => $questions,
        ];
    }

    public function export($id)
    {
        $periode = MasterPeriode::findOrFail($id);

        return Excel::download(
            new class($periode->id, $this) implements FromView {
                protected $periodeId;
                protected $controller;

                public function __construct($periodeId, $controller)
                {
                    $this->periodeId = $periodeId;
                    $this->controller = $controller;
                }

                public function view(): \Illuminate\Contracts\View\View
                {
                    $data = $this->controller->buildReportDataPublic($this->periodeId, true);

                    return view('Admin.GeneralReport.template-report', $data);
                }
            },
            'report-survey-' . $periode->id . '.xlsx'
        );
    }

    public function indexProject(Request $request)
    {
        $query = MasterPeriode::query();
        $query = $this->searchByDate($query, $request);
        $periode = $query->paginate(10)->withQueryString();

        return view('Admin.GeneralReport.admin-report-project', compact('periode'));
    }

    public function exportProject($id)
    {
        $periode = MasterPeriode::findOrFail($id);

        return Excel::download(
            new class($periode->id, $this) implements FromView {
                protected $periodeId;
                protected $controller;

                public function __construct($periodeId, $controller)
                {
                    $this->periodeId = $periodeId;
                    $this->controller = $controller;
                }

                public function view(): \Illuminate\Contracts\View\View
                {
                    $data = $this->controller->buildReportDataPublic($this->periodeId, false);

                    return view('Admin.GeneralReport.template-report', $data);
                }
            },
            'report-survey-project-' . $periode->id . '.xlsx'
        );
    }

    /**
     * Wrapper public karena anonymous class di atas hanya bisa memanggil
     * method public milik controller induknya.
     */
    public function buildReportDataPublic(int $periodeId, bool $outletMode): array
    {
        return $this->buildReportData($periodeId, $outletMode);
    }
    /**
     * Show the form for editing the specified resource.
     */
}
