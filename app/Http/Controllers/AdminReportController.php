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
                        ->whereNotNull('r.master_outlet_survey_id') // 👈 Tambahkan ini agar hanya mengambil yang punya outlet ID
                        ->groupBy(
                            'p.pertanyaan',
                            'o.options',
                            'r.jenis_pertanyaan_id'
                        )
                        ->get();

                    // 🔹 total respondent
                    $totalBlesscon = \App\Models\MasterRespondent::where('periode_id', $this->periodeId)
                        ->where('jenis_pertanyaan_id', 1)
                        ->whereNotNull('master_outlet_survey_id') // 👈 Tambahkan ini agar hanya mengambil yang punya outlet ID
                        ->count();

                    $totalSuperior = \App\Models\MasterRespondent::where('periode_id', $this->periodeId)
                        ->where('jenis_pertanyaan_id', 2)
                        ->whereNotNull('master_outlet_survey_id') // 👈 Tambahkan ini agar hanya mengambil yang punya outlet ID
                        ->count();

                        $totalRespondent = $totalBlesscon + $totalSuperior;
                    return view('Admin.Report.template-report', [
                        'answers' => $answers,
                        'totalBlesscon' => $totalBlesscon,
                        'totalSuperior' => $totalSuperior,
                        'totalRespondent' => $totalRespondent
                    ]);
                }
            },

            'report-survey-' . $periode->id . '.xlsx'
        );
    }
    /**
     * UNTUK FITUR DI PROJECT
     */
    public function indexProject(Request $request)
    {
        $query = MasterPeriode::query();

        // panggil function search
        $query = $this->searchByDate($query, $request);

        $periode = $query->paginate(1)->withQueryString();

        return view('Admin.Report.admin-report-project', compact('periode'));
    }

    public function exportProject($id)
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
                    // 🔹 Ambil data jawaban (Khusus Project: master_outlet_survey_id IS NULL)
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
                        ->whereNull('r.master_outlet_survey_id') // 👈 Mengambil data yang NULL (Project)
                        ->groupBy(
                            'p.pertanyaan',
                            'o.options',
                            'r.jenis_pertanyaan_id'
                        )
                        ->get();

                    // 🔹 Total respondent Blesscon (Project)
                    $totalBlesscon = \App\Models\MasterRespondent::where('periode_id', $this->periodeId)
                        ->where('jenis_pertanyaan_id', 1)
                        ->whereNull('master_outlet_survey_id') // 👈 Diubah ke whereNull
                        ->count();

                    // 🔹 Total respondent Superior (Project)
                    $totalSuperior = \App\Models\MasterRespondent::where('periode_id', $this->periodeId)
                        ->where('jenis_pertanyaan_id', 2)
                        ->whereNull('master_outlet_survey_id') // 👈 Diubah ke whereNull
                        ->count();

                    $totalRespondent = $totalBlesscon + $totalSuperior;
                    return view('Admin.Report.template-report', [
                        'answers' => $answers,
                        'totalBlesscon' => $totalBlesscon,
                        'totalSuperior' => $totalSuperior,
                        'totalRespondent' => $totalRespondent
                    ]);
                }
            },

            'report-survey-project-' . $periode->id . '.xlsx'
        );
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
