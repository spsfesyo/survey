<?php

namespace App\Http\Controllers;

use App\Models\MasterPeriode;
use App\Services\OutletQualifiedReportService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromView;

class OutletQualifiedReportController extends Controller
{


    protected OutletQualifiedReportService $service;

    public function __construct(OutletQualifiedReportService $service)
    {
        $this->service = $service;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MasterPeriode::query();

        if ($request->filled('tanggal')) {
            $tanggal = $request->tanggal;
            $query->whereDate('tanggal_mulai', '<=', $tanggal)
                ->whereDate('tanggal_selesai', '>=', $tanggal);
        }

        $periode = $query->orderBy('id', 'asc')->paginate(5)->withQueryString();

        return view('Admin.GeneralReport.admin-report-qualified-outlet', compact('periode'));
    }

    public function exportReport($id)
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
                    $data = $this->controller->buildReportDataPublic($this->periodeId);
                    // ⬇️ Tambahkan label khusus laporan qualified
                    $data['labelQty'] = '≥100 m3 AAC & sudah isi survei';

                    return view('Admin.GeneralReport.template-report', $data);
                }
            },
            'report-survey-outlet-qualified-' . $periode->id . '.xlsx'
        );
    }

    /**
     * Wrapper public — supaya anonymous class bisa akses.
     */
    public function buildReportDataPublic(int $periodeId): array
    {
        $idsLolos = $this->service->idOutletLolos($periodeId);

        return $this->service->buildReportData($periodeId, $idsLolos, 'outlet');
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
