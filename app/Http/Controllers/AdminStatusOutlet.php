<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasterOutletSurvey;
use App\Models\MasterPeriode;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromView;
use App\Exports\MasterOutletSurveyExport;

class AdminStatusOutlet extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index(Request $request)
    // {
    //     $role = auth()->user()->role;
    //     $search = $request->search;
    //     $status = MasterOutletSurvey::with([
    //         // 'area.provinsi', 'area.kabupaten.provinsi'



    //         'kabupaten.provinsi',
    //         'kabupaten.area'
    //     ])
    //         // ->whereBetween('id', [1, 18])
    //         ->where('status_blast_wa', true)
    //         ->when($search, function ($query) use ($search) {
    //             $query->where('nama_outlet', 'like', "%{$search}%")
    //                 ->orWhere('kode_unik', 'like', "%{$search}%")
    //                 ->orWhereHas('kabupaten', function ($q) use ($search) {
    //                     $q->where('nama_kabupaten', 'like', "%{$search}%");
    //                 });
    //         })

    //         ->orderBy('id', 'asc')
    //         ->paginate(10);


    //     return view('Admin.admin-status-outlet', compact('status', 'role', 'search'));
    // }

    /**
     * Show the form for creating a new resource.
     */

    public function index(Request $request)
    {
        $query = MasterPeriode::query();

        $periode = $query->orderBy('id', 'asc')
            ->paginate(5)
            ->withQueryString();
        return view('Admin.admin-status-outlet', compact('periode'));
    }

    public function export()
    {
        return Excel::download(new MasterOutletSurveyExport, 'status_outlet.xlsx');
    }

    public function EnableAllStatusCode()
    {
        MasterOutletSurvey::where('status_kode_unik', 'N')
            ->update(['status_kode_unik' => 'Y']);
        return redirect()->back()->with('success', 'Semua kode unik berhasil aktif semua');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function DisableAllStatusCode()
    {
        MasterOutletSurvey::where('status_kode_unik', 'Y')
            ->update(['status_kode_unik' => 'N']);
        return redirect()->back()->with('success', 'Semua kode unik berhasil nonaktif semua');
    }




    public function exportStatusOutlet($id)
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
                    // $data = DB::table('master_respondent as r')
                    //     ->join('master_outlet_survey as os', 'r.master_outlet_survey_id', '=', 'os.id')
                    //     ->leftJoin('master_provinsi as pv', 'r.provinsi_id', '=', 'pv.id')
                    //     ->leftJoin('master_kabupaten as kb', 'r.master_kabupaten_id', '=', 'kb.id')
                    //     ->leftJoin('master_area as ar', 'r.area_id', '=', 'ar.id')
                    //     ->leftJoin('master_periode_survey as p', 'r.periode_id', '=', 'p.id')
                    //     ->select(
                    //         'os.nama_outlet',
                    //         'os.sps_internal_name as nama_outlet_console',
                    //         'os.telepone_outlet',
                    //         'os.kode_unik',
                    //         DB::raw("CONCAT(p.tanggal_mulai, ' - ', p.tanggal_selesai) as periode"),
                    //         DB::raw('COALESCE(pv.nama_provinsi, "-") as provinsi'),
                    //         DB::raw('COALESCE(kb.nama_kabupaten, "-") as kabupaten'),
                    //         DB::raw('COALESCE(ar.nama_area, "-") as area')
                    //     )
                    //     ->where('r.periode_id', $this->periodeId)
                    //     ->whereNotNull('r.periode_id') // 👈 syarat: periode tidak null
                    //     ->orderBy('os.nama_outlet')
                    //     ->get();

                    // return view('Admin.GeneralReport.template-status-outlet', [
                    //     'data' => $data,
                    // ]);

                    // 1. Daftar pertanyaan (sama seperti view index)
                    $pertanyaanList = \App\Models\MasterPertanyaan::orderBy('id')->get();

                    // 2. Respondent + relasi lengkap
                    $data = \App\Models\MasterRespondent::with([
                        'outletSurvey',
                        'provinsi',
                        'kabupaten',
                        'area',
                        'answers.options',
                        'JenisPertanyaan',
                        'periode',
                    ])
                        ->where('periode_id', $this->periodeId)
                        ->whereNotNull('periode_id')
                        ->orderBy('id')
                        ->get();

                    return view('Admin.GeneralReport.template-status-outlet', [
                        'data'           => $data,
                        'pertanyaanList' => $pertanyaanList,
                    ]);
                }
            },

            'status-outlet-periode-' . $periode->id . '.xlsx'
        );
    }



    /**
     * ============================================================
     * FUNGSI 1: Ambil data periode dari master_periode
     * ============================================================
     * Cuma wrapper tipis supaya error "periode tidak ditemukan"
     * langsung jelas kalau ID yang dikirim salah.
     */

    // private function getPeriode($periodeId): MasterPeriode
    // {
    //     return MasterPeriode::findOrFail($periodeId);
    // }


    /**
     * ============================================================
     * FUNGSI 2: Query ke SQL Server, hitung total qty AAC per outlet
     * ============================================================
     * Input : rentang tanggal (dari periode yang dipilih)
     * Output: array of object, tiap object = 1 outlet dengan:
     *         - SPSName  (nama outlet di sisi SQL Server)
     *         - TotalQty (total quantity AAC dalam rentang tanggal itu)
     *
     * Cuma outlet yang TotalQty >= $minQty yang ikut keluar
     * (difilter langsung di database lewat HAVING, bukan di PHP,
     * supaya lebih cepat & tidak boros memory).
     */

    // private function queryOutletQtyFromSqlServer(
    //     string $tanggalMulai,
    //     string $tanggalSelesai,
    //     float $minQty = 100
    // ): array {
    //     $hasil = DB::connection('sqlsrv')->select("
    //         SELECT
    //             m.SPSName,
    //             SUM(TRY_CAST(d.Quantity AS FLOAT)) AS TotalQty
    //         FROM MARKETING..ODLN o
    //         LEFT JOIN MARKETING..DLN1 d ON o.DocEntry = d.DocEntry
    //         LEFT JOIN DBMOLIVE..M_OutletConsolidation m ON m.OutletCode = o.U_VOutlet
    //         WHERE o.CANCELED = 'N'
    //           AND d.U_V_VERIANTITEM = 'AAC'
    //           AND o.DocDate >= CONVERT(DATE, ?, 23)
    //           AND o.DocDate < DATEADD(DAY, 1, CONVERT(DATE, ?, 23))
    //           AND m.SPSName IS NOT NULL
    //         GROUP BY m.SPSName
    //         HAVING SUM(TRY_CAST(d.Quantity AS FLOAT)) >= ?
    //         ORDER BY TotalQty DESC
    //     ", [$tanggalMulai, $tanggalSelesai, $minQty]);

    //     return $hasil;
    // }


    /**
     * ============================================================
     * FUNGSI 3 (MAIN / TESTING): Gabungkan semuanya, return hasil
     * ============================================================
     * Ini fungsi utama yang kamu panggil untuk CEK HASILNYA dulu
     * (belum nyambung ke master_respondent, cuma data mentah dari
     * SQL Server). Return-nya JSON supaya gampang dibaca di browser.
     *
     * Cara akses (setelah route didaftarkan):
     * GET /test/outlet-100m3/{id_periode}
     */

    // public function cekOutletArray($periodeId)
    // {
    //     $periode = $this->getPeriode($periodeId);

    //     $daftarOutlet = $this->queryOutletQtyFromSqlServer(
    //         $periode->tanggal_mulai,
    //         $periode->tanggal_selesai,
    //         100   // <-- batas minimum qty, gampang diubah di sini kalau perlu
    //     );

    //     // 🔹 Response JSON: biar kamu bisa langsung lihat di browser,
    //     // tanpa perlu buat view/export dulu.
    //     return response()->json([
    //         'periode_id'       => $periode->id,
    //         'tanggal_mulai'    => $periode->tanggal_mulai,
    //         'tanggal_selesai'  => $periode->tanggal_selesai,
    //         'jumlah_outlet'    => count($daftarOutlet),   // <-- INI yang kamu cari: berapa outlet yang lolos
    //         'daftar_outlet'    => $daftarOutlet,           // detail tiap outlet + total qty-nya
    //     ]);
    // }



    /**
     * Display the specified resource.
     */
    // public function updateStatusCode(Request $request)
    // {
    //     $request->validate([
    //         'id' => 'required',
    //         'status' => 'required|in:Y,N',
    //     ]);

    //     MasterOutletSurvey::where('id', $request->id)
    //         ->update(['status_kode_unik' => $request->status]);

    //     return redirect()->back()->with('success', 'Status berhasil diperbarui');
    // }

    /**
     * Show the form for editing the specified resource.
     */
    // public function RegenerateUnikCode()
    // {

    //     DB::statement("
    //     UPDATE master_outlet_survey
    //     SET kode_unik = UPPER(SUBSTRING(REPLACE(UUID(), '-', ''), 1, 10))");

    //     return redirect()->back()->with('success', 'Kode unik berhasil diregenerasi ulang');
    // }

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
