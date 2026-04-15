<?php

namespace App\Http\Controllers;

use App\Models\MasterHadiah;
use Illuminate\Http\Request;
use App\Models\MasterPeriode;
use App\Models\MasterProvinsi;
use App\Models\MasterKabupaten;
use App\Models\MasterAreaSurvey;
use App\Models\MasterOutletSurvey;
use App\Models\MasterPlotPemenang;
use App\Models\PlotHadiahSurvey;

class AdminKonfigController extends Controller
{
    public function index()
    {
        $provinsi = MasterProvinsi::orderBy('nama_provinsi')->get();
        $kabupaten = MasterKabupaten::orderBy('nama_kabupaten')->get();
        $area = MasterAreaSurvey::orderBy('nama_area')->get();
        $periode = MasterPeriode::paginate(5, ['*'], 'periode_page');
        $periodeAktif = MasterPeriode::where('status', 'aktif')->get();
        $hadiahAktif = MasterHadiah::where('status', 'Y')->get();
        $hadiah = MasterHadiah::with('periode')->paginate(5, ['*'], 'hadiah_page');
        $plot = PlotHadiahSurvey::with(['provinsi', 'kabupaten', 'area', 'outletSurvey', 'hadiah', 'periode'])->paginate(5, ['*'], 'plot_page');
        $outlets    = MasterOutletSurvey::orderBy('nama_outlet')->get();
        $role = auth()->user()->role;
        // info($role);

        return view('admin.admin-konfig', compact('provinsi', 'kabupaten', 'area', 'periode', 'hadiah', 'plot', 'outlets', 'periodeAktif', 'hadiahAktif', 'role'));
    }

    public function PeriodeCreate(Request $request)
    {

        $request->validate([
            'nama_periode' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            // 'status' => 'required|in:0,1',
        ]);


        MasterPeriode::create([
            'nama_periode' => $request->nama_periode,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'status' => 'nonaktif',
            // 'status' => $request->status,
        ]);
        return redirect()->route('konfig-survey')->with('success', 'Periode survey berhasil ditambahkan.');
        // return redirect()->route('konfig-survey')->with('success', 'Periode survey berhasil ditambahkan.');
    }

    public function PeriodeUpdate(Request $request)
    {
        $request->validate([
            'nama_periode' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'status' => 'required|in:aktif,nonaktif',
        ]);


        $periode = MasterPeriode::findOrFail($request->id);
        $periode->update([
            'nama_periode' => $request->nama_periode,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'status' => $request->status,
        ]);

        // dd($periode->all());
        return redirect()->route('konfig-survey')->with('success', 'Periode survey berhasil diperbarui.');
    }


    // untuk hadiah

    public function HadiahCreate(Request $request)
    {
        $request->validate([
            'nama_hadiah' => 'required|string|max:255',
            'kode_hadiah' => 'required|string|max:255',
            'periode_survey_id' => 'required|exists:master_periode_survey,id',
            'jumlah_hadiah' => 'required|integer|min:1',
            'status' => 'required|in:Y,N',
        ]);

        MasterHadiah::create([
            'nama_hadiah' => $request->nama_hadiah,
            'kode_hadiah' => $request->kode_hadiah,
            'periode_survey_id' => $request->periode_survey_id, // sesuai nama kolom di tabel
            'jumlah_hadiah' => $request->jumlah_hadiah,
            'status' => $request->status,
        ]);

        return redirect()->route('konfig-survey')->with('success', 'Hadiah berhasil ditambahkan!');
    }

    public function HadiahUpdate(Request $request)
    {
        $request->validate([
            'nama_hadiah' => 'required|string|max:255',
            'kode_hadiah' => 'required|string|max:255',
            'periode_survey_id' => 'required|exists:master_periode_survey,id',
            'jumlah_hadiah' => 'required|integer|min:1',
            'status' => 'required|in:Y,N',
        ]);

        $hadiah = MasterHadiah::findOrFail($request->id);
        $hadiah->update([
            'nama_hadiah' => $request->nama_hadiah,
            'kode_hadiah' => $request->kode_hadiah,
            'periode_survey_id' => $request->periode_survey_id,
            'jumlah_hadiah' => $request->jumlah_hadiah,
            'status' => $request->status,
        ]);

        return redirect()->route('konfig-survey')->with('success', 'Hadiah berhasil diperbarui!');
    }



    // fungsi untuk transaksi jumlah hadiah plot dan hadiah

    private function updateHadiahStock($oldHadiahId, $newHadiahId)
    {
        // Jika hadiah diganti atau dihapus, kembalikan stok hadiah lama
        if ($oldHadiahId && $oldHadiahId != $newHadiahId) {
            MasterHadiah::where('id', $oldHadiahId)->increment('jumlah_hadiah', 1);
        }

        // Jika ada hadiah baru, kurangi stok hadiah baru
        if ($newHadiahId && $oldHadiahId != $newHadiahId) {
            $hadiahBaru = MasterHadiah::find($newHadiahId);

            if (!$hadiahBaru) {
                throw new \Exception("Hadiah tidak ditemukan.");
            }

            if ($hadiahBaru->jumlah_hadiah <= 0) {
                throw new \Exception("Stok hadiah sudah habis. Tidak bisa melakukan plotting.");
            }

            // Kurangi stok
            $hadiahBaru->decrement('jumlah_hadiah', 1);
        }
    }




    public function PlotCreate(Request $request)
    {
        $request->validate([
            'filter_type' => 'required',
            'jumlah_outlet' => 'required|integer|min:1',
            'periode_survey_id' => 'required|exists:master_periode_survey,id',
        ]);

        $filterType = $request->filter_type;
        $jumlah = $request->jumlah_outlet;
        $periodeId = $request->periode_survey_id;

        // Ambil query outlet dasar
        $query = MasterOutletSurvey::query();

        // Filter berdasarkan pilihan
        if ($filterType === 'provinsi') {
            $request->validate(['provinsi_id' => 'required']);
            $provinsiId = $request->provinsi_id;

            $query->whereHas('kabupaten', function ($q) use ($provinsiId) {
                $q->where('provinsi_id', $provinsiId);
            });
        } elseif ($filterType === 'kabupaten') {
            $request->validate(['kabupaten_id' => 'required']);
            $query->where('master_kabupaten_id', $request->kabupaten_id);
        } elseif ($filterType === 'area') {
            $request->validate(['area_id' => 'required']);
            $query->where('master_area_id', $request->area_id);
        }

        // Ambil outlet random
        $outletTerpilih = $query->inRandomOrder()->limit($jumlah)->get();

        if ($outletTerpilih->count() === 0) {
            return back()->with('error', 'Tidak ada outlet ditemukan berdasarkan filter.');
        }

        // Simpan ke plot_hadiah_survey
        foreach ($outletTerpilih as $outlet) {
            PlotHadiahSurvey::create([
                'periode_survey_id' => $periodeId, // sesuaikan
                'provinsi_id' => $outlet->kabupaten->provinsi_id,
                'master_kabupaten_id' => $outlet->master_kabupaten_id,
                'master_area_id' => $outlet->master_area_id,
                'master_outlet_survey_id' => $outlet->id,
                'hadiah_id' => null, // isi sesuai kebutuhan
                'is_winning' => 'N',
                'status_respondent_assigned' => 'N',
            ]);
        }

        return back()->with('success', 'Plot berhasil dibuat!');
    }

    public function getPlotData($id)
    {
        $plot = PlotHadiahSurvey::with(['periode', 'hadiah'])->findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => $plot
        ]);
    }

    public function updatePlot(Request $request, $id)
    {
        $plot = PlotHadiahSurvey::findOrFail($id);

        $oldHadiahId = $plot->hadiah_id;
        $newHadiahId = $request->hadiah_id;

        // ---- Panggil fungsi stok ----
        $this->updateHadiahStock($oldHadiahId, $newHadiahId);
        // -----------------------------

        $plot->update([
            'periode_survey_id' => $request->periode_survey_id,
            'hadiah_id' => $request->hadiah_id,
        ]);

        return redirect()->back()->with('success', 'Berhasil update data!');
    }


    public function deletePlot($id)
    {
        $plot = PlotHadiahSurvey::findOrFail($id);

        if ($plot->hadiah_id) {
            MasterHadiah::where('id', $plot->hadiah_id)->increment('jumlah_hadiah', 1);
        }

        $plot->delete();

        return back()->with('success', 'Plot dihapus dan stok hadiah dikembalikan.');
    }




    // public function PlotCreate(Request $request)
    // {
    //     $request->validate([
    //         'filter_type' => 'required|in:provinsi,kabupaten,area',
    //         'jumlah_outlet' => 'required|integer|min:1',
    //     ]);

    //     $periodeAktif = MasterPeriode::where('status', 'aktif')->first();

    //     if (!$periodeAktif) {
    //         return redirect()->back()->with('error', 'Tidak ada periode aktif.');
    //     }

    //     $filterType = $request->filter_type;
    //     $jumlah = $request->jumlah_outlet;

    //     // Ambil outlet lengkap dengan relasinya
    //     $query = MasterOutletSurvey::with(['provinsiThroughArea', 'kabupaten', 'area']);

    //     if ($filterType === 'provinsi' && $request->provinsi_id) {
    //         $query->where('provinsi_id', $request->provinsi_id);
    //     } elseif ($filterType === 'kabupaten' && $request->kabupaten_id) {
    //         $query->where('kabupaten_id', $request->kabupaten_id);
    //     } elseif ($filterType === 'area' && $request->area_id) {
    //         $query->where('master_area_id', $request->area_id);
    //     }

    //     $outletTerpilih = $query->inRandomOrder()->limit($jumlah)->get();

    //     if ($outletTerpilih->isEmpty()) {
    //         return redirect()->back()->with('error', 'Tidak ada outlet yang ditemukan untuk filter tersebut.');
    //     }

    //     foreach ($outletTerpilih as $outlet) {
    //         MasterPlotPemenang::create([
    //             'periode_survey_id' => $periodeAktif->id,
    //             'provinsi_id' => optional($outlet->area->kabupaten->provinsi)->id,
    //             'master_kabupaten_id' => optional($outlet->area->kabupaten)->id,
    //             'master_area_id' => optional($outlet->area)->id,
    //             'master_outlet_survey_id' => $outlet->id,
    //             'hadiah_id' => null,
    //             'is_winning' => 'N',
    //             'status_respondent_assigned' => 'N',
    //         ]);
    //     }

    //     return redirect()->back()->with('success', 'Plot hadiah berhasil dibuat.');
    // }

}
