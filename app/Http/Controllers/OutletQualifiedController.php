<?php

namespace App\Http\Controllers;

use App\Models\MasterPeriode;
use App\Models\MasterPertanyaan;
use App\Models\MasterRespondent;
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
    public function __construct(private OutletQualifiedService $service) {}


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

        // Daftar pertanyaan diambil SEKALI, dipakai untuk susun baris & header template
        $pertanyaanList = MasterPertanyaan::orderBy('id')->get();

        $baris  = $this->susunBaris($hasil, $pertanyaanList);
        $label  = (string) $periode->tanggal_mulai . ' - ' . (string) $periode->tanggal_selesai;
        $minQty = $hasil['min_qty'];

        return Excel::download(
            new class($baris, $label, $minQty, $pertanyaanList) implements FromView {
                protected $baris;
                protected $label;
                protected $minQty;
                protected $pertanyaanList;

                public function __construct($baris, $label, $minQty, $pertanyaanList)
                {
                    $this->baris          = $baris;
                    $this->label          = $label;
                    $this->minQty         = $minQty;
                    $this->pertanyaanList = $pertanyaanList;
                }

                public function view(): \Illuminate\Contracts\View\View
                {
                    return view('Admin.GeneralReport.template-outlet-qualified', [
                        'baris'          => $this->baris,
                        'periode'        => $this->label,
                        'minQty'         => $this->minQty,
                        'pertanyaanList' => $this->pertanyaanList,
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
    /**
     * Susun baris Excel: 1 baris per toko (SPS).
     * Data diambil dari baris master_outlet_survey yang punya respondent,
     * lengkap dengan jawaban survey, foto, dan identitas respondent.
     */
    private function susunBaris(array $hasil, $pertanyaanList): array
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

        if (empty($idBarisIsi)) {
            return [];
        }

        // Ambil semua respondent + relasi lengkap, dikelompokkan per outlet survey
        $respondents = MasterRespondent::with([
            'answers.options',
            'provinsi',
            'kabupaten',
            'area',
            'outletSurvey',
            'JenisPertanyaan',
        ])
            ->where('periode_id', $periodeId)
            ->whereIn('master_outlet_survey_id', $idBarisIsi)
            ->orderBy('id')
            ->get()
            ->groupBy('master_outlet_survey_id');

        $baris = [];

        foreach ($hasil['lolos'] as $sps) {
            $barisIsi = array_values(array_filter(
                $sps['outlet_survey'],
                fn($b) => $b['jumlah_respondent'] > 0
            ));

            if (empty($barisIsi)) {
                continue;
            }

            // Kumpulkan respondent dari SEMUA baris outlet survey milik SPS ini
            $respondentList = collect();
            foreach ($barisIsi as $b) {
                $respondentList = $respondentList->merge(
                    $respondents->get($b['outlet_survey_id'], collect())
                );
            }
            $respondentList = $respondentList->sortBy('id')->values();
            $respondent     = $respondentList->first();
            // dd([
            //     'jumlah_respondent_terbaca' => $respondentList->count(),
            //     'respondent_attr'           => $respondent?->only([
            //         'id',
            //         'nama_respondent',
            //         'nama_toko_respondent',
            //         'alamat_toko_respondent',
            //         'telepone_respondent',
            //         'provinsi_id',
            //         'master_kabupaten_id',
            //         'jenis_pertanyaan_id',
            //     ]),
            //     'pertanyaan_tipe5' => $pertanyaanList
            //         ->where('master_tipe_pertanyaan_id', 5)
            //         ->map(fn($p) => [
            //             'id'        => $p->id,
            //             'pertanyaan' => $p->pertanyaan,
            //             'tipe'      => $p->master_tipe_pertanyaan_id,
            //             'reference' => $p->reference,
            //         ])->values(),
            //     'jumlah_answers' => $respondent?->answers->count(),
            // ]);

            // Susun jawaban per pertanyaan
            $jawaban = [];

            foreach ($pertanyaanList as $pertanyaan) {
                $nilaiList = [];

                foreach ($respondentList as $r) {
                    if ($pertanyaan->master_tipe_pertanyaan_id == 5 && $pertanyaan->reference) {
                        // Tipe reference: ambil dari kolom/relasi master_respondent
                        switch ($pertanyaan->reference) {
                            case 'provinsi_id':
                                $nilai = $r->provinsi->nama_provinsi ?? null;
                                break;
                            case 'master_kabupaten_id':
                                $nilai = $r->kabupaten->nama_kabupaten ?? null;
                                break;
                            case 'jenis_pertanyaan_id':
                                $nilai = $r->JenisPertanyaan->jenis_pertanyaan ?? null;
                                break;
                            default:
                                $nilai = $r->{$pertanyaan->reference} ?? null;
                        }
                        $nilaiList[] = $nilai;
                    } else {
                        // Tipe biasa: ambil dari tabel answers
                        foreach ($r->answers->where('master_pertanyaan_id', $pertanyaan->id) as $ans) {
                            $option = $ans->options->options ?? null;

                            if (in_array(strtolower((string) $option), ['other', 'lainnya'], true)) {
                                $option = $ans->lainnya ?? 'Other';
                            }

                            $nilaiList[] = $option ?? ($ans->jawaban_teks ?? $ans->lainnya);
                        }
                    }
                }

                $nilaiList = array_unique(array_filter($nilaiList, fn($v) => $v !== null && $v !== ''));
                $jawaban[$pertanyaan->id] = $nilaiList ? implode(' / ', $nilaiList) : '-';
            }

            $baris[] = [
                'nama_outlet'         => $this->gabung($barisIsi, 'nama_outlet'),
                'nama_outlet_console' => $sps['sps_name'],
                'telepone_outlet'     => $this->gabung($barisIsi, 'telepone_outlet'),
                'kode_unik'           => $this->gabung($barisIsi, 'kode_unik'),
                'total_qty'           => $sps['total_qty'],
                'jumlah_respondent'   => $sps['jumlah_respondent'],
                'area'                => $respondent->area->nama_area ?? '-',
                'jawaban'             => $jawaban,
                'foto_url'            => $respondent && $respondent->foto_selfie
                    ? asset($respondent->foto_selfie)
                    : '-',
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
