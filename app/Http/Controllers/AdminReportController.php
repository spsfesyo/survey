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

    public function index(Request $request)
    {
        $query = MasterPeriode::query();

        // panggil function search
        $query = $this->searchByDate($query, $request);

        $periode = $query->paginate(1)->withQueryString();

        return view('Admin.Report.admin-report', compact('periode'));
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
     * Show the form for creating a new resource.
     */
    // public function export($id)
    // {
    //     $periode = MasterPeriode::findOrFail($id);

    //     return Excel::download(

    //         new class($periode->id) implements FromArray {

    //             protected $periodeId;

    //             public function __construct($periodeId)
    //             {
    //                 $this->periodeId = $periodeId;
    //             }

    //             public function array(): array
    //             {
    //                 // 🔹 ambil data jawaban
    //                 $answers = DB::table('answer_survey as a')
    //                     ->join('pertanyaan_options as o', 'a.pertanyaan_options_id', '=', 'o.id')
    //                     ->join('master_respondent as r', 'a.master_respondent_id', '=', 'r.id')
    //                     ->join('master_pertanyaan as p', 'a.master_pertanyaan_id', '=', 'p.id')
    //                     ->select(
    //                         'p.pertanyaan',
    //                         'o.options as options',
    //                         'r.jenis_pertanyaan_id',
    //                         DB::raw('COUNT(*) as total')
    //                     )
    //                     ->where('r.periode_id', $this->periodeId)
    //                     ->whereIn('p.master_tipe_pertanyaan_id', [1, 2])
    //                     ->groupBy(
    //                         'p.pertanyaan',
    //                         'o.options',
    //                         'r.jenis_pertanyaan_id'
    //                     )
    //                     ->get();

    //                 // 🔹 total respondent
    //                 $totalBlesscon = MasterRespondent::where('periode_id', $this->periodeId)
    //                     ->where('jenis_pertanyaan_id', 1)
    //                     ->count();

    //                 $totalSuperior = MasterRespondent::where('periode_id', $this->periodeId)
    //                     ->where('jenis_pertanyaan_id', 2)
    //                     ->count();

    //                 $result = [];

    //                 foreach ($answers->groupBy('pertanyaan') as $pertanyaan => $items) {

    //                     // judul pertanyaan
    //                     $result[] = [$pertanyaan];
    //                     $result[] = [];

    //                     // BLESSCON
    //                     $result[] = ["BLESSCON ({$totalBlesscon})"];

    //                     foreach ($items->where('jenis_pertanyaan_id', 1) as $row) {
    //                         $result[] = [
    //                             $row->options,
    //                             $row->total
    //                         ];
    //                     }

    //                     $result[] = [];

    //                     // SUPERIOR
    //                     $result[] = ["SUPERIOR ({$totalSuperior})"];

    //                     foreach ($items->where('jenis_pertanyaan_id', 2) as $row) {
    //                         $result[] = [
    //                             $row->options,
    //                             $row->total
    //                         ];
    //                     }

    //                     $result[] = [];
    //                     $result[] = [];
    //                 }

    //                 return $result;
    //             }
    //         },

    //         'report-survey-' . $periode->id . '.xlsx'
    //     );
    // }




    public function export($id)
    {
        $periode = MasterPeriode::findOrFail($id);

        return Excel::download(

            new class($periode->id) implements FromView {

                protected $periodeId;

                public function __construct($periodeId)
                {
                    $this->periodeId = $periodeId;
                }

                public function view(): \Illuminate\Contracts\View\View
                {
                    // 🔹 ambil data
                    $answers = DB::table('answer_survey as a')
                    ->join('pertanyaan_options as o', 'a.pertanyaan_options_id', '=', 'o.id')
                    ->join('master_respondent as r', 'a.master_respondent_id', '=', 'r.id')
                    ->join('master_pertanyaan as p', 'a.master_pertanyaan_id', '=', 'p.id')
                    ->select(
                            'p.pertanyaan',
                            'o.options as option_text',
                            'r.jenis_pertanyaan_id',
                            DB::raw('COUNT(*) as total')
                        )
                        ->where('r.periode_id', $this->periodeId)
                        ->groupBy(
                            'p.pertanyaan',
                            'o.options',
                            'r.jenis_pertanyaan_id'
                        )
                        ->get();

                    // 🔹 total respondent
                    $totalBlesscon = \App\Models\MasterRespondent::where('periode_id', $this->periodeId)
                        ->where('jenis_pertanyaan_id', 1)
                        ->count();

                    $totalSuperior = \App\Models\MasterRespondent::where('periode_id', $this->periodeId)
                        ->where('jenis_pertanyaan_id', 2)
                        ->count();

                    return view('Admin.Report.template-report', [
                        'answers' => $answers,
                        'totalBlesscon' => $totalBlesscon,
                        'totalSuperior' => $totalSuperior
                    ]);
                }
            },

            'report-survey-' . $periode->id . '.xlsx'
        );
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
