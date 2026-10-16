<?php

namespace App\Http\Controllers;

use App\Models\MasterPeriode;
use App\Services\OutletQualifiedService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Facades\Excel;


class OutletQualifiedController extends Controller
{

    /**
     * FUNGSI 1: Ambil periode dari master_periode.
     */
   public function __construct(private OutletQualifiedService $service)
    {
    }


    /**
     * MENU 1 - halaman daftar periode (tombol Download per baris).
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

        return view('Admin.admin-outlet-qualified', compact('periode'));
    }


    /**
     * Untuk TESTING saja (JSON). Hapus atau lindungi route-nya kalau sudah selesai,
     * karena menampilkan data penjualan.
     */
    public function cekOutletTerdaftarSurvey($periodeId)
    {
        $hasil   = $this->service->cari((int) $periodeId);
        $periode = $hasil['periode'];

        return response()->json([
            'periode_id'      => $periode->id,
            'tanggal_mulai'   => (string) $periode->tanggal_mulai,
            'tanggal_selesai' => (string) $periode->tanggal_selesai,
            'ringkasan'       => $hasil['ringkasan'],
            'outlet_lolos'    => $hasil['lolos'],
            'contoh_tidak_ditemukan_di_mysql' => array_slice($hasil['tidak_ditemukan'], 0, 50),
        ]);
    }


    /**
     * MENU 1 - download Excel: toko >= 100 m3 yang SUDAH isi survei di periode terpilih.
     * Satu baris = satu toko (nama SPS).
     */
    public function export($id)
    {
        $hasil   = $this->service->cari((int) $id);
        $periode = $hasil['periode'];

        $baris = $this->susunBaris($hasil);
        $label = (string) $periode->tanggal_mulai . ' - ' . (string) $periode->tanggal_selesai;
        $minQty = $hasil['min_qty'];

        return Excel::download(
            new class($baris, $label, $minQty) implements FromView {
                protected $baris;
                protected $label;
                protected $minQty;

                public function __construct($baris, $label, $minQty)
                {
                    $this->baris  = $baris;
                    $this->label  = $label;
                    $this->minQty = $minQty;
                }

                public function view(): \Illuminate\Contracts\View\View
                {
                    return view('Admin.GeneralReport.template-outlet-qualified', [
                        'baris'  => $this->baris,
                        'periode' => $this->label,
                        'minQty' => $this->minQty,
                    ]);
                }
            },
            'outlet-100m3-sudah-survei-periode-' . $periode->id . '.xlsx'
        );
    }


    /**
     * Susun baris Excel: 1 baris per toko, data diambil dari baris
     * master_outlet_survey yang punya respondent; lokasi dari master_respondent.
     */
    private function susunBaris(array $hasil): array
    {
        $periodeId = $hasil['periode']->id;

        // ID baris outlet survey yang punya respondent di periode ini
        $idBarisIsi = [];
        foreach ($hasil['lolos'] as $sps) {
            foreach ($sps['outlet_survey'] as $b) {
                if ($b['jumlah_respondent'] > 0) {
                    $idBarisIsi[] = $b['outlet_survey_id'];
                }
            }
        }

        $lokasi = DB::table('master_respondent as r')
            ->leftJoin('master_provinsi as pv', 'r.provinsi_id', '=', 'pv.id')
            ->leftJoin('master_kabupaten as kb', 'r.master_kabupaten_id', '=', 'kb.id')
            ->leftJoin('master_area as ar', 'r.area_id', '=', 'ar.id')
            ->where('r.periode_id', $periodeId)
            ->whereIn('r.master_outlet_survey_id', $idBarisIsi)
            ->select(
                'r.master_outlet_survey_id',
                DB::raw('MAX(pv.nama_provinsi) as provinsi'),
                DB::raw('MAX(kb.nama_kabupaten) as kabupaten'),
                DB::raw('MAX(ar.nama_area) as area')
            )
            ->groupBy('r.master_outlet_survey_id')
            ->get()
            ->keyBy('master_outlet_survey_id');

        $baris = [];

        foreach ($hasil['lolos'] as $sps) {
            $barisIsi = array_values(array_filter(
                $sps['outlet_survey'],
                fn ($b) => $b['jumlah_respondent'] > 0
            ));

            $lok = $lokasi->get($barisIsi[0]['outlet_survey_id']);

            $baris[] = [
                'nama_outlet'         => $this->gabung($barisIsi, 'nama_outlet'),
                'nama_outlet_console' => $sps['sps_name'],
                'telepone_outlet'     => $this->gabung($barisIsi, 'telepone_outlet'),
                'kode_unik'           => $this->gabung($barisIsi, 'kode_unik'),
                'total_qty'           => $sps['total_qty'],
                'jumlah_respondent'   => $sps['jumlah_respondent'],
                'provinsi'            => $lok->provinsi ?? '-',
                'kabupaten'           => $lok->kabupaten ?? '-',
                'area'                => $lok->area ?? '-',
            ];
        }

        return $baris;
    }


    /** Gabungkan nilai unik dari beberapa baris, dipisah " / " */
    private function gabung(array $baris, string $kolom): string
    {
        return implode(' / ', array_unique(array_filter(array_column($baris, $kolom))));
    }
}
