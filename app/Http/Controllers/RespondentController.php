<?php

namespace App\Http\Controllers;

use App\Models\AnswerSurvey;
use App\Models\MasterJenisPertanyaan;
use App\Models\MasterKabupaten;
use App\Models\MasterKotaSurvey;
use App\Models\MasterOutletSurvey;
use App\Models\MasterPeriode;
use App\Models\MasterPertanyaan;
use App\Models\MasterProvinsi;
use App\Models\MasterRespondent;
use App\Models\PlotHadiahSurvey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;

class RespondentController extends Controller
{
    public function index(Request $request, $token = null)
    {
        // try {

        //     // 1️⃣ Ambil token (query prioritas)
        //     $token = $request->store ?? $token;

        //     if (!$token) {
        //         abort(403, 'Token tidak ada');
        //     }

        //     // 2️⃣ Clean token
        //     // $token = trim($token);
        //     // $token = preg_replace('/[^A-Za-z0-9=\/+]/', '', $token);

        //     // 3️⃣ Decrypt
        //     $decrypt = Crypt::decryptString($token);

        //     // 4️⃣ Parsing fleksibel
        //     $data = explode('|', $decrypt);

        //     if (count($data) == 2) {
        //         // format: id|spsName
        //         $idFormVisitSales = $data[0];
        //         $spsName = $data[1];
        //     } else {
        //         // format: hanya spsName
        //         $idFormVisitSales = null;
        //         $spsName = $decrypt;
        //     }

        //     // 5️⃣ Cari outlet
        //     $outlet = MasterOutletSurvey::where('sps_internal_name', $spsName)->first();

        //     if (!$outlet) {
        //         abort(404, 'Outlet tidak ditemukan');
        //     }

        //     session([
        //         'raw_token' => $token,
        //         'redirect_url' => $request->fullUrl(),
        //     ]);

        //     // 6️⃣ Simpan session
        //     session([
        //         'master_outlet_survey_id' => $outlet->id,
        //         'visit_sales_form_id' => $idFormVisitSales, // boleh null
        //         'sps_name' => $spsName,
        //     ]);


        //     // 7️⃣ Redirect ke form
        //     return redirect()->route('form-utama');
        // } catch (\Exception $e) {
        //     Log::error('Decrypt gagal: ' . $e->getMessage());

        //     return redirect('/')
        //         ->with('error', 'Link tidak valid atau sudah expired.');
        // }


        try {

            // ✅ pisahkan token & store
            $pathToken = $token;
            $store = $request->store;

            if (!$store || !$pathToken) {
                abort(403, 'Token tidak lengkap');
            }

            // ✅ decrypt dari STORE (bukan token)
            $decrypt = Crypt::decryptString($store);

            $data = explode('|', $decrypt);

            if (count($data) == 2) {
                $idFormVisitSales = $data[0];
                $spsName = $data[1];
            } else {
                $idFormVisitSales = null;
                $spsName = $decrypt;
            }

            $outlet = MasterOutletSurvey::where('sps_internal_name', $spsName)->first();

            if (!$outlet) {
                abort(404, 'Outlet tidak ditemukan');
            }

            // ✅ simpan untuk redirect nanti
            session([
                'redirect_token' => $pathToken,
                'redirect_store' => $store,
            ]);

            // session utama
            session([
                'master_outlet_survey_id' => $outlet->id,
                'visit_sales_form_id' => $idFormVisitSales,
                'sps_name' => $spsName,
            ]);

            return redirect()->route('form-utama');
        } catch (\Exception $e) {
            Log::error('Decrypt gagal: ' . $e->getMessage());

            return redirect('/')
                ->with('error', 'Link tidak valid atau sudah expired.');
        }
    }

    // public function create(Request $request)
    // {
    //     $request->validate([
    //         'kode_unik' => 'required|string|size:10'
    //     ]);

    //     $outlet = MasterOutletSurvey::where('kode_unik', $request->kode_unik)
    //         ->where('status_kode_unik', 'Y')
    //         ->first();

    //     if (!$outlet) {
    //         return redirect()->back()->with('error', 'Kode unik tidak valid atau sudah digunakan.');
    //     }

    //     Session::put('master_outlet_survey_id', $outlet->id);
    //     Session::put('kode_unik', $request->kode_unik);

    //     return redirect()->route('form-utama')->with('success', 'Kode diterima. Silakan isi form.');
    // }

    public function getFormUtama()
    {


        // $outletId = session('master_outlet_survey_id');

        // $outlet = MasterOutletSurvey::find($outletId);

        // if (!$outlet) {
        //     return redirect()->route('home');
        // }


        // $selectedKabupaten = MasterKabupaten::find($outlet->master_kabupaten_id);

        // $selectedProvinsi = $selectedKabupaten
        //     ? MasterProvinsi::find($selectedKabupaten->provinsi_id)
        //     : null;

        // $pertanyaanFormUtama = MasterPertanyaan::with([
        //     'tipePertanyaan',
        //     'options'
        // ])
        //     ->where('master_section_id', 1)
        //     ->orderBy('order')
        //     ->get();

        // $merekBataRingan = MasterJenisPertanyaan::all();

        // return view('form-utama', compact(
        //     'outlet',
        //     'selectedKabupaten',
        //     'selectedProvinsi',
        //     'pertanyaanFormUtama',
        //     'merekBataRingan'
        // ));


        // Ambil ID Outlet dari session (jika ada, biasanya dari /visit-entry/{token})
        $outletId = session('master_outlet_survey_id');
        $outlet = null;
        $selectedKabupaten = null;
        $selectedProvinsi = null;
        $provinsis = null; // Untuk alur umum/inputan select

        if ($outletId) {
            // --- ALUR A: JIKA MASUK LEWAT VISIT SALES (READONLY) ---
            $outlet = MasterOutletSurvey::find($outletId);
            if ($outlet) {
                $selectedKabupaten = MasterKabupaten::find($outlet->master_kabupaten_id);
                $selectedProvinsi = $selectedKabupaten
                    ? MasterProvinsi::find($selectedKabupaten->provinsi_id)
                    : null;
            }
        }

        // --- ALUR B: JIKA TIDAK ADA OUTLET DI SESSION (INPUTAN/SELECT) ---
        if (!$outlet) {
            // Ambil data master semua provinsi untuk dropdown select
            $provinsis = MasterProvinsi::all();
        }

        // Data Pertanyaan dan Merek tetap di-load untuk kedua alur
        $pertanyaanFormUtama = MasterPertanyaan::with(['tipePertanyaan', 'options'])
            ->where('master_section_id', 1)
            ->orderBy('order')
            ->get();

        $merekBataRingan = MasterJenisPertanyaan::all();

        return view('form-utama', compact(
            'outlet',
            'selectedKabupaten',
            'selectedProvinsi',
            'provinsis', // Variabel baru untuk menampung semua list provinsi
            'pertanyaanFormUtama',
            'merekBataRingan'
        ));
    }



    public function answerFormUtama(Request $request)
    {
        $request->validate([
            'telepone_respondent' => 'required|digits_between:10,15',
            // validasi lainnya jika perlu
        ]);

        if (!$request->has('master_outlet_survey_id') || empty($request->input('master_outlet_survey_id'))) {
            $rules['nama_toko_project'] = 'required|string|max:255';
            $rules['provinsi']          = 'required|integer';
            $rules['kabupaten']         = 'required|integer';
            $rules['alamat_toko_project'] = 'required|string';
        }

        // $telp = $request->input('telepone_respondent');

        // // Cek duplikasi nomor di database
        // if (\App\Models\MasterRespondent::where('telepone_respondent', $telp)->exists()) {
        //     return back()
        //         ->withInput()
        //         ->with('phone_duplicate', $telp);
        // }

        // Simpan session jika valid
        session(['form_utama' => $request->all()]);

        return redirect()->route('form-pertanyaan-kualitas');
    }


    public function getFormProject()
    {
        // Alur Project Tim biasanya bersifat umum/bebas isi, mirip Alur B
        // Kita panggil semua list provinsi agar user bisa milih lokasi project-nya
        $provinsis = MasterProvinsi::all();
        $outlet = null;
        $selectedKabupaten = null;
        $selectedProvinsi = null;

        $pertanyaanFormUtama = MasterPertanyaan::with(['tipePertanyaan', 'options'])
            ->where('master_section_id', 1) // Sesuaikan id section jika form project pertanyaannya berbeda
            ->orderBy('order')
            ->get();

        $merekBataRingan = MasterJenisPertanyaan::all();

        // Re-use (gunakan kembali) blade 'form-utama' agar tidak double coding layout
        return view('form-utama', compact(
            'outlet',
            'selectedKabupaten',
            'selectedProvinsi',
            'provinsis',
            'pertanyaanFormUtama',
            'merekBataRingan'
        ));
    }


    public function getKabupatenByProvinsi($provinsiId)
    {
        try {
            $kabupaten = MasterKabupaten::where('provinsi_id', $provinsiId)->get();

            return response()->json([
                'status' => 'success',
                'data' => $kabupaten
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }


    public function getFormKualitas()
    {
        // $oldData = session('form_utama', []);
        $pertanyaanFormKualitas = MasterPertanyaan::with(['tipePertanyaan', 'options'])
            ->where('master_section_id', 2)
            ->orderBy('order')
            ->get();

        // dd($pertanyaanFormKualitas);

        return view('form-pertanyaan-kualitas', compact('pertanyaanFormKualitas'));
    }

    public function answerFormKualitas(Request $request)
    {
        $validated = $request->all();
        session(['form_kualitas' => $validated]);

        return redirect()->route('form-pertanyaan-harga');
    }

    public function getFormHarga()
    {

        $pertanyaanFormHarga = MasterPertanyaan::with(['tipePertanyaan', 'options'])
            ->where('master_section_id', 3)
            ->orderBy('order')
            ->get();

        // dd($pertanyaanFormHarga);


        return view('form-pertanyaan-harga', compact('pertanyaanFormHarga'));
    }

    public function answerFormHarga(Request $request)
    {
        $validated = $request->all();
        session(['form_harga' => $validated]);
        return redirect()->route('form-pertanyaan-pengiriman');
    }

    public function getFormPengiriman()
    {

        $pertanyaanFormPengiriman = MasterPertanyaan::with(['tipePertanyaan', 'options'])
            ->where('master_section_id', 4)
            ->orderBy('order')
            ->get();

        // dd($pertanyaanFormPengiriman);

        return view('form-pertanyaan-pengiriman', compact('pertanyaanFormPengiriman'));
    }

    public function answerFormPengiriman(Request $request)
    {
        $validated = $request->all();
        session(['form_pengiriman' => $validated]);
        return redirect()->route('form-pertanyaan-pelayanan');
    }

    public function getFormPelayanan()
    {

        $pertanyaanFormPelayanan = MasterPertanyaan::with(['tipePertanyaan', 'options'])
            ->where('master_section_id', 5)
            ->orderBy('order')
            ->get();

        // dd($pertanyaanFormPelayanan);

        return view('form-pertanyaan-pelayanan', compact('pertanyaanFormPelayanan'));
    }

    public function answerFormPelayanan(Request $request)
    {
        $validated = $request->all();
        session(['form_pelayanan' => $validated]);

        // lanjut ke gimmick
        return redirect()->route('form-pertanyaan-gimmick'); // ✅ BENAR
    }

    public function getFormGimmick()
    {
        return view('form-pertanyaan-gimmick');
    }

    public function answerFormGimmick(Request $request)
    {
        $request->validate([
            'is_gimmick' => 'required|in:0,1'
        ]);

        // simpan ke session
        session([
            'form_gimmick' => [
                'is_gimmick' => $request->is_gimmick
            ]
        ]);

        // lanjut ke submit final
        return redirect()->route('submit-final');
    }


    // public function answerFormPelayanan(Request $request)
    // {
    //     $validated = $request->all();
    //     session(['form_pelayanan' => $validated]);
    //     // return redirect()->route('form-pertanyaan-pelayanan');
    // }


    // public function submitFinalAnswer(Request $request)
    // {
    //     $formUtama = session('form_utama', []);
    //     $phone = $formUtama['telepone_respondent'] ?? null;
    //     if ($phone && MasterRespondent::where('telepone_respondent', $phone)->exists()) {

    //         // $jawabanSebelumnya = session('jawaban');
    //         // session(['jawaban' => $jawabanSebelumnya]);
    //         return redirect()
    //             ->route('form-pertanyaan-pelayanan')  // ⛔ ini redirect ke form terakhir
    //             ->with('phone_duplicate', $phone)
    //             ->withInput();
    //     }

    //     // 1️⃣ Simpan sesi form pelayanan
    //     session(['form_pelayanan' => $request->all()]);

    //     // 2️⃣ Ambil semua sesi form
    //     $formUtama      = session('form_utama', []);
    //     $formKualitas   = session('form_kualitas', []);
    //     $formHarga      = session('form_harga', []);
    //     $formPengiriman = session('form_pengiriman', []);
    //     $formPelayanan  = session('form_pelayanan', []);

    //     // 3️⃣ Cek duplikat no. telepon sebelum eksekusi lainnya

    //     // 4️⃣ Ambil sesi kode unik & outlet
    //     $outletId = session('master_outlet_survey_id');
    //     $kodeUnik = session('kode_unik');
    //     if (!$outletId || !$kodeUnik || empty($formUtama)) {
    //         return redirect()->route('home')->with('error', 'Sesi tidak lengkap. Silakan ulangi.');
    //     }

    //     // 5️⃣ Proses foto (base64)
    //     $fotoPath = null;
    //     if ($request->filled('foto_base64')) {
    //         try {
    //             $base64 = preg_replace('#^data:image/\w+;base64,#i', '', $request->foto_base64);
    //             $data = base64_decode($base64);
    //             $fileName = 'foto-respondent/' . Str::uuid() . '.jpg';
    //             Storage::disk('public')->put($fileName, $data);
    //             $fotoPath = 'storage/' . $fileName;
    //         } catch (\Throwable $e) {
    //             logger('Foto gagal disimpan: ' . $e->getMessage());
    //         }
    //     }

    //     // 6️⃣ Ambil provinsi & kabupaten
    //     $provId = $formUtama['provinsi'] ?? null;
    //     $kabId  = $formUtama['kabupaten'] ?? null;


    //     // 7️⃣ Update outlet dengan kode unik (main logic)


    //     if ($kabId) {
    //         // Ambil periode dan kabupaten dari master_outlet_survey
    //         $outletSurvey = MasterOutletSurvey::find($outletId);
    //         $periodeId = $outletSurvey ? $outletSurvey->periode : null;
    //         $outletKabupatenId = $outletSurvey ? $outletSurvey->master_kabupaten_id : null;

    //         // ✅ Double check: pastikan kabupaten input sesuai dengan kabupaten di outlet survey
    //         $isKabupatenMatch = ($outletKabupatenId == $kabId);

    //         // Cari baris yang match outlet, kabupaten, dan periode
    //         $slot = MasterRespondent::where('master_kabupaten_id', $kabId)
    //             ->where('master_outlet_survey_id', $outletId)
    //             ->first();

    //         if (!$slot && $isKabupatenMatch) {
    //             // Coba cari berdasarkan kabupaten saja (outlet belum di-plot)
    //             // HANYA jika kabupaten sesuai dengan outlet survey
    //             $slot = MasterRespondent::where('master_kabupaten_id', $kabId)
    //                 ->whereNull('master_outlet_survey_id')
    //                 ->first();
    //         }

    //         if ($slot && $isKabupatenMatch) {
    //             // Update slot yang ada HANYA jika kabupaten sesuai
    //             $respondent = $slot;
    //             $respondent->update([
    //                 'master_outlet_survey_id'  => $outletId,
    //                 'periode_id'               => $periodeId,
    //                 'telepone_respondent'      => $phone,
    //                 'provinsi_id'              => $provId,
    //                 'nama_respondent'          => $formUtama['nama_respondent'] ?? null,
    //                 'nama_toko_respondent'     => $formUtama['nama_toko_respondent'] ?? null,
    //                 'alamat_toko_respondent'   => $formUtama['alamat_toko_respondent'] ?? null,
    //                 'jenis_pertanyaan_id'      => $formUtama['jenis_pertanyaan_id'] ?? null,
    //                 'foto_selfie'              => $fotoPath,
    //             ]);
    //         } else {
    //             // Buat baris baru jika:
    //             // 1. Tidak ada slot yang sesuai, ATAU
    //             // 2. Kabupaten tidak sesuai dengan outlet survey (salah input kabupaten)
    //             $respondent = MasterRespondent::create([
    //                 'master_outlet_survey_id'  => $outletId,
    //                 'master_kabupaten_id'      => $kabId,
    //                 'periode_id'               => $periodeId,
    //                 'telepone_respondent'      => $phone,
    //                 'provinsi_id'              => $provId,
    //                 'jenis_pertanyaan_id'       => $formUtama['jenis_pertanyaan_id'] ?? null,
    //                 'nama_respondent'          => $formUtama['nama_respondent'] ?? null,
    //                 'nama_toko_respondent'     => $formUtama['nama_toko_respondent'] ?? null,
    //                 'alamat_toko_respondent'   => $formUtama['alamat_toko_respondent'] ?? null,
    //                 'foto_selfie'              => $fotoPath,
    //             ]);
    //         }

    //         // Jika user dapat hadiah, tandai slot sebelumnya INACTIVE
    //         if ($respondent->hadiah_id) {
    //             // Ubah respondent lainnya jadi INACTIVE
    //             MasterRespondent::where('master_kabupaten_id', $kabId)
    //                 ->where('master_outlet_survey_id', $outletId)
    //                 ->where('id', '!=', $respondent->id)
    //                 ->update(['status_hadiah' => 'INACTIVE']);

    //             // Tambahkan baris ini untuk mengubah respondent saat ini
    //             $respondent->status_hadiah = 'INACTIVE';
    //             $respondent->save();
    //         }
    //     } else {
    //         // Ambil periode dari master_outlet_survey untuk fallback case
    //         $outletSurvey = MasterOutletSurvey::find($outletId);
    //         $periodeId = $outletSurvey ? $outletSurvey->periode : null;

    //         // Kasus jika tidak ada kabupaten tetap masuk (fallback)
    //         $respondent = MasterRespondent::create([
    //             'master_outlet_survey_id'  => $outletId,
    //             'master_kabupaten_id'      => null,
    //             'periode_id'               => $periodeId,
    //             'telepone_respondent'      => $phone,
    //             'provinsi_id'              => $provId,
    //             'jenis_pertanyaan_id'       => $formUtama['jenis_pertanyaan_id'] ?? null,
    //             'nama_respondent'          => $formUtama['nama_respondent'] ?? null,
    //             'nama_toko_respondent'     => $formUtama['nama_toko_respondent'] ?? null,
    //             'alamat_toko_respondent'   => $formUtama['alamat_toko_respondent'] ?? null,
    //             'foto_selfie'              => $fotoPath,
    //         ]);
    //     }


    //     $respondentId = $respondent->id;

    //     // 8️⃣ Simpan jawaban ke AnswerSurvey
    //     $all = array_filter(array_merge($formUtama, $formKualitas, $formHarga, $formPengiriman, $formPelayanan));
    //     foreach ($all as $key => $val) {
    //         if (!str_starts_with($key, 'pertanyaan_') || str_contains($key, 'other_')) continue;
    //         $qId = str_replace('pertanyaan_', '', $key);
    //         if (is_array($val)) {
    //             foreach ($val as $opt) {
    //                 $isOther = str_starts_with($opt, 'other_');
    //                 AnswerSurvey::create([
    //                     'master_respondent_id'   => $respondentId,
    //                     'master_pertanyaan_id'   => $qId,
    //                     'pertanyaan_options_id'  => $isOther ? intval(str_replace('other_', '', $opt)) : intval($opt),
    //                     'jawaban_teks'           => $isOther ? null : ($all["teks_$qId"] ?? null),
    //                     'lainnya'                => $isOther ? ($all["pertanyaan_{$qId}_$opt"] ?? null) : null,
    //                 ]);
    //             }
    //         } else {
    //             AnswerSurvey::create([
    //                 'master_respondent_id'   => $respondentId,
    //                 'master_pertanyaan_id'   => $qId,
    //                 'pertanyaan_options_id'  => is_numeric($val) ? intval($val) : null,
    //                 'jawaban_teks'           => is_numeric($val) ? ($all["teks_$qId"] ?? null) : $val,
    //                 'lainnya'                => $all["lainnya_$qId"] ?? null,
    //             ]);
    //         }
    //     }

    //     // 9️⃣ Update kode unik outlet jadi 'N'
    //     MasterOutletSurvey::where('id', $outletId)
    //         ->update(['status_kode_unik' => 'N']);

    //     // 🔟 Ambil nama hadiah untuk SweetAlert
    //     $hadiahNama = optional($respondent->hadiah)->nama_hadiah;

    //     // 1️⃣1️⃣ Bersihkan sesi
    //     session()->forget(['form_utama', 'form_kualitas', 'form_harga', 'form_pengiriman', 'form_pelayanan', 'master_outlet_survey_id', 'kode_unik']);

    //     // 1️⃣2️⃣ Redirect dengan SweetAlert
    //     if ($hadiahNama) {
    //         return redirect()->route('home')->with('success', "Selamat! Anda mendapatkan hadiah: $hadiahNama");
    //     }
    //     return redirect()->route('home')->with('success', 'Terima kasih atas partisipasi Anda!, Maaf anda belum beruntung memenangkan hadiah menarik dari kami.');
    // }

    // public function submitFinalAnswer(Request $request)
    // {

    // dd('kambing');
    /* =======================
     * 1️⃣ CEK DUPLIKAT PHONE
     * ======================= */
    // $formUtama = session('form_utama', []);
    // $phone = $formUtama['telepone_respondent'] ?? null;

    // if ($phone && MasterRespondent::where('telepone_respondent', $phone)->exists()) {
    //     return redirect()
    //         ->route('form-pertanyaan-pelayanan')
    //         ->with('phone_duplicate', $phone)
    //         ->withInput();
    // }

    /* =======================
     * 2️⃣ SIMPAN SESSION FORM TERAKHIR
     * ======================= */
    // session(['form_gimmick' => $request->all()]);
    // (1)
    // $formUtama      = session('form_utama', []);
    // $formKualitas   = session('form_kualitas', []);
    // $formHarga      = session('form_harga', []);
    // $formPengiriman = session('form_pengiriman', []);
    // $formPelayanan  = session('form_pelayanan', []);
    // $formGimmick    = session('form_gimmick', []);

    // $outletId = session('master_outlet_survey_id');
    // $kodeUnik = session('kode_unik');

    // if (!$outletId || !$kodeUnik || empty($formUtama)) {
    //     // return redirect()->route('home')->with('error', 'Sesi tidak lengkap. Silakan ulangi.');
    //      return redirect('/')->with('error', 'Sesi tidak lengkap. Silakan ulangi.');

    // }

    // if (!$outletId) {
    //     return redirect('/')->with('error', 'Outlet tidak ditemukan. Ulangi dari awal.');
    // }

    // if (empty($formUtama)) {
    //     return redirect()->route('form-utama')
    //         ->with('error', 'Silakan isi form utama terlebih dahulu.');
    // }

    // (2)
    // if (!$outletId || empty($formUtama)) {
    //     return redirect('/')
    //         ->with('error', 'Sesi tidak lengkap. Silakan ulangi dari awal.');
    // }

    /* =======================
     * 3️⃣ PROSES FOTO
     * ======================= */
    // (3)
    // $fotoPath = null;
    // if ($request->filled('foto_base64')) {
    //     try {
    //         $base64 = preg_replace('#^data:image/\w+;base64,#i', '', $request->foto_base64);
    //         $data = base64_decode($base64);
    //         $fileName = 'foto-respondent/' . Str::uuid() . '.jpg';
    //         Storage::disk('public')->put($fileName, $data);
    //         $fotoPath = 'storage/' . $fileName;
    //     } catch (\Throwable $e) {
    //         logger('Foto gagal disimpan: ' . $e->getMessage());
    //     }
    // }

    /* =======================
     * 4️⃣ SIMPAN RESPONDENT (SELALU)
     * ======================= */
    // (4)
    // $provId = $formUtama['provinsi'] ?? null;
    // $kabId  = $formUtama['kabupaten'] ?? null;

    // $outletSurvey = MasterOutletSurvey::find($outletId);



    // bagian ini sama seperti bawahnya hanya saja untuk lebih advance mengecek periode aktif
    // $periodeAktif = MasterPeriode::whereDate('tanggal_mulai', '<=', now())
    //     ->whereDate('tanggal_selesai', '>=', now())
    //     ->first();


    // untuk cek mencari periode yang aktif
    // (5)
    // $periodeAktif = MasterPeriode::where('status', 'aktif')->first();

    // if (!$periodeAktif) {
    //     return redirect()->route('home')
    //         ->with('error', 'Periode tidak aktif.');
    // }

    // $periodeId = $periodeAktif->id;

    // $respondent = MasterRespondent::create([
    //     'master_outlet_survey_id'  => $outletId,
    //     'master_kabupaten_id'      => $kabId,
    //     'periode_id'               => $periodeId,
    //     // 'telepone_respondent'      => $phone,
    //     'telepone_respondent'      => $formUtama['telepone_respondent'] ?? null,
    //     'provinsi_id'              => $provId,
    //     'jenis_pertanyaan_id'      => $formUtama['jenis_pertanyaan_id'] ?? null,
    //     'nama_respondent'          => $formUtama['nama_respondent'] ?? null,
    //     'nama_toko_respondent'     => $formUtama['nama_toko_respondent'] ?? null,
    //     'alamat_toko_respondent'   => $formUtama['alamat_toko_respondent'] ?? null,
    //     'foto_selfie'              => $fotoPath,
    //     'is_gimmick'               => $formGimmick['is_gimmick'] ?? null,
    // ]);

    /* =======================
     * 5️⃣ CEK KE TABEL PLOT (LOGIC MENANG)
     * ======================= */

    // $hadiahNama = null;

    // if ($kabId && $provId && $periodeId) {

    //     $plot = PlotHadiahSurvey::where('master_outlet_survey_id', $outletId)
    //         ->where('master_kabupaten_id', $kabId)
    //         ->where('provinsi_id', $provId)
    //         ->where('periode_survey_id', $periodeId)
    //         ->where('is_winning', 'N')
    //         ->first();

    //     if ($plot) {
    //         // 🎉 MENANG
    //         $plot->update([
    //             'is_winning'       => 'Y',
    //             'status_respondent_assigned' => 'Y',
    //             'respondent_id' => $respondent->id,
    //             'tanggal_menang'   => now()->toDateString(),
    //         ]);

    //         $respondent->update([
    //             'hadiah_id'  => $plot->hadiah_id,
    //             'is_winner'  => 'Y',
    //         ]);

    //         $hadiahNama = optional($plot->hadiah)->nama_hadiah;
    //     }
    // }

    /* =======================
     * 6️⃣ SIMPAN JAWABAN
     * ======================= */
    // (6)
    // $respondentId = $respondent->id;
    // $all = array_filter(array_merge($formUtama, $formKualitas, $formHarga, $formPengiriman, $formPelayanan));

    // foreach ($all as $key => $val) {
    //     if (!str_starts_with($key, 'pertanyaan_') || str_contains($key, 'other_')) continue;
    //     $qId = str_replace('pertanyaan_', '', $key);

    //     if (is_array($val)) {
    //         foreach ($val as $opt) {
    //             $isOther = str_starts_with($opt, 'other_');
    //             AnswerSurvey::create([
    //                 'master_respondent_id'   => $respondentId,
    //                 'master_pertanyaan_id'   => $qId,
    //                 'pertanyaan_options_id'  => $isOther ? intval(str_replace('other_', '', $opt)) : intval($opt),
    //                 'jawaban_teks'           => $isOther ? null : ($all["teks_$qId"] ?? null),
    //                 'lainnya'                => $isOther ? ($all["pertanyaan_{$qId}_$opt"] ?? null) : null,
    //             ]);
    //         }
    //     } else {
    //         AnswerSurvey::create([
    //             'master_respondent_id'   => $respondentId,
    //             'master_pertanyaan_id'   => $qId,
    //             'pertanyaan_options_id'  => is_numeric($val) ? intval($val) : null,
    //             'jawaban_teks'           => is_numeric($val) ? ($all["teks_$qId"] ?? null) : $val,
    //             'lainnya'                => $all["lainnya_$qId"] ?? null,
    //         ]);
    //     }
    // }

    /* =======================
     * 7️⃣ UPDATE KODE UNIK OUTLET
     * ======================= */
    // (7)
    // MasterOutletSurvey::where('id', $outletId)
    //     ->update(['status_kode_unik' => 'N']);




    /* =======================
     * 8️⃣ CLEAR SESSION
     * ======================= */
    // $visitSalesId = session('visit_sales_id');
    // $spsName = session('sps_name');

    // $encrypted = Crypt::encryptString(
    //     $visitSalesId . '|' . $spsName
    // );

    // session()->forget([
    //     'form_utama',
    //     'form_kualitas',
    //     'form_harga',
    //     'form_pengiriman',
    //     'form_pelayanan',
    //     'form_gimmick',
    //     'master_outlet_survey_id',
    //     'kode_unik'
    // ]);


    // (8)
    // $visitSalesId = session('visit_sales_form_id');
    // $spsName = session('sps_name');

    // if ($visitSalesId) {
    //     $payload = $visitSalesId . '|' . $spsName;
    // } else {
    //     $payload = $spsName;
    // }


    // (9)
    // session()->forget([
    //     'form_utama',
    //     'form_kualitas',
    //     'form_harga',
    //     'form_pengiriman',
    //     'form_pelayanan',
    //     'form_gimmick',
    //     'master_outlet_survey_id',
    //     'kode_unik',
    // ]);

    /* =======================
     * 9️⃣ REDIRECT + SWEETALERT
     * ======================= */
    //     if ($hadiahNama) {
    //         return redirect()->route('home')
    //             ->with('success', "🎉 Selamat! Anda mendapatkan hadiah: $hadiahNama");
    //     }

    //     return redirect()->route('home')
    //         ->with('success', 'Terima kasih atas partisipasi Anda! Anda belum beruntung kali ini.');
    // }

    // return redirect()->away(
    //     'https://esstesting.edpapp.com:2096/form_kunjungan_pelanggan/edit/' . urlencode($encrypted)
    // );


    //(ini sudah hampir benar namun visit salse harus mendecrypt ulang)
    // return redirect()->away(
    //     'https://esstesting.edpapp.com:2096/form_kunjungan_pelanggan/edit?token=' . urlencode($encrypted)
    // );


    // (10)
    // $token = session('redirect_token');
    // $store = session('redirect_store');

    // if (!$token || !$store) {
    //     return redirect('/')->with('error', 'Token redirect tidak ditemukan.');
    // }

    // $url = 'https://esstesting.edpapp.com:2096/form_kunjungan_pelanggan/edit/'
    //     . $token
    //     . '?store=' . urlencode($store);

    // return redirect()->away($url);








    // return redirect('/dummy-finish?token=' . urlencode($encrypted));

    // return redirect()->away(
    //     'http://127.0.0.1:8000/dummy-finish?token=' . urlencode($encrypted)
    // );


    // public function checkDuplicatePhone(Request $request)
    // {
    //     $phone = $request->query('phone');
    //     $exists = MasterRespondent::where('telepone_respondent', $phone)->exists();
    //     return response()->json(['exists' => $exists]);
    // }
    // }

    // dicek untuk flow kedua dan flow pertama jangan sampai mengacaukan flow pertama


    public function submitFinalAnswer(Request $request)
    {
        /* ====================================================
     * 1️⃣ AMBIL DATA SESI FORM
     * ==================================================== */
        $formUtama      = session('form_utama', []);
        $formKualitas   = session('form_kualitas', []);
        $formHarga      = session('form_harga', []);
        $formPengiriman = session('form_pengiriman', []);
        $formPelayanan  = session('form_pelayanan', []);
        $formGimmick    = session('form_gimmick', []);

        $outletId = session('master_outlet_survey_id'); // Ini hanya ada di Flow 1 (Link Otomatis)
        $kodeUnik = session('kode_unik');

        // PENYESUAIAN VALIDASI AKSES:
        // Form Utama tidak boleh kosong, tetapi $outletId BOLEH kosong (karena Flow 2 tidak punya outletId)
        if (empty($formUtama)) {
            return redirect('/')
                ->with('error', 'Sesi tidak lengkap. Silakan ulangi dari awal.');
        }

        /* ====================================================
     * 2️⃣ PROSES FOTO BASE64
     * ==================================================== */
        $fotoPath = null;
        if ($request->filled('foto_base64')) {
            try {
                $base64 = preg_replace('#^data:image/\w+;base64,#i', '', $request->foto_base64);
                $data = base64_decode($base64);
                $fileName = 'foto-respondent/' . Str::uuid() . '.jpg';
                Storage::disk('public')->put($fileName, $data);
                $fotoPath = 'storage/' . $fileName;
            } catch (\Throwable $e) {
                logger('Foto gagal disimpan: ' . $e->getMessage());
            }
        }

        /* ====================================================
     * 3️⃣ AMBIL PERIODE AKTIF SURVEY
     * ==================================================== */
        $periodeAktif = MasterPeriode::where('status', 'aktif')->first();

        if (!$periodeAktif) {
            return redirect('/')
                ->with('error', 'Periode survey tidak aktif.');
        }

        $periodeId = $periodeAktif->id;

        /* ====================================================
     * 4️⃣ LOGIKA INSERT DATA RESPONDENT (FLOW 1 vs FLOW 2)
     * ==================================================== */

        // Ambil ID Provinsi & Kabupaten dari inputan form utama
        $provId = $formUtama['provinsi'] ?? null;
        $kabId  = $formUtama['kabupaten'] ?? null;

        // Siapkan array data yang akan disimpan ke MasterRespondent
        $insertData = [
            'master_kabupaten_id'     => $kabId,
            'periode_id'              => $periodeId,
            'telepone_respondent'     => $formUtama['telepone_respondent'] ?? null,
            'provinsi_id'             => $provId,
            'jenis_pertanyaan_id'     => $formUtama['jenis_pertanyaan_id'] ?? null,
            'nama_respondent'         => $formUtama['nama_respondent'] ?? null,
            'foto_selfie'             => $fotoPath,
            'is_gimmick'              => $formGimmick['is_gimmick'] ?? null,
        ];

        // PERCABANGAN BERDASARKAN FLOW:
        if (!empty($outletId)) {
            // [FLOW 1] Jika lewat Link Otomatis (Ada ID Outlet Master)
            $insertData['master_outlet_survey_id']  = $outletId;
            $insertData['nama_toko_respondent']     = $formUtama['nama_toko_respondent'] ?? null;
            $insertData['alamat_toko_respondent']   = $formUtama['alamat_toko_respondent'] ?? null;

            // Kolom project manual di-set null
            $insertData['nama_customer_project']    = null;
            $insertData['nama_toko_project']        = null;
            $insertData['alamat_toko_project']      = null;
        } else {
            // [FLOW 2] Jika lewat Input Manual (Tidak membawa ID Outlet)
            $insertData['master_outlet_survey_id']  = null; // Karena sudah diubah jadi nullable()
            $insertData['nama_toko_respondent']     = null;
            $insertData['alamat_toko_respondent']   = null;

            // Isi ke kolom-kolom baru hasil manual input project
            $insertData['nama_customer_project']    = $formUtama['nama_customer_project'] ?? null;
            $insertData['nama_toko_project']        = $formUtama['nama_toko_project'] ?? null;
            $insertData['alamat_toko_project']      = $formUtama['alamat_toko_project'] ?? null;
        }

        // Eksekusi Insert Ke Database
        $respondent = MasterRespondent::create($insertData);
        $respondentId = $respondent->id;

        /* ====================================================
     * 5️⃣ SIMPAN JAWABAN PERTANYAAN SURVEY
     * ==================================================== */
        $all = array_filter(array_merge($formUtama, $formKualitas, $formHarga, $formPengiriman, $formPelayanan));

        foreach ($all as $key => $val) {
            if (!str_starts_with($key, 'pertanyaan_') || str_contains($key, 'other_')) continue;
            $qId = str_replace('pertanyaan_', '', $key);

            if (is_array($val)) {
                foreach ($val as $opt) {
                    $isOther = str_starts_with($opt, 'other_');
                    AnswerSurvey::create([
                        'master_respondent_id'   => $respondentId,
                        'master_pertanyaan_id'   => $qId,
                        'pertanyaan_options_id'  => $isOther ? intval(str_replace('other_', '', $opt)) : intval($opt),
                        'jawaban_teks'           => $isOther ? null : ($all["teks_$qId"] ?? null),
                        'lainnya'                => $isOther ? ($all["pertanyaan_{$qId}_$opt"] ?? null) : null,
                    ]);
                }
            } else {
                AnswerSurvey::create([
                    'master_respondent_id'   => $respondentId,
                    'master_pertanyaan_id'   => $qId,
                    'pertanyaan_options_id'  => is_numeric($val) ? intval($val) : null,
                    'jawaban_teks'           => is_numeric($val) ? ($all["teks_$qId"] ?? null) : $val,
                    'lainnya'                => $all["lainnya_$qId"] ?? null,
                ]);
            }
        }

        /* ====================================================
     * 6️⃣ UPDATE KODE UNIK (HANYA UNTUK FLOW 1)
     * ==================================================== */
        if (!empty($outletId)) {
            MasterOutletSurvey::where('id', $outletId)
                ->update(['status_kode_unik' => 'N']);
        }

        /* ====================================================
     * 7️⃣ CLEAR SESSION SURVEY
     * ==================================================== */
        session()->forget([
            'form_utama',
            'form_kualitas',
            'form_harga',
            'form_pengiriman',
            'form_pelayanan',
            'form_gimmick',
            'master_outlet_survey_id',
            'kode_unik',
        ]);

        /* ====================================================
     * 8️⃣ REDIRECT REDIRECT KE URL EKSTERNAL / FINISH
     * ==================================================== */
        $token = session('redirect_token');
        $store = session('redirect_store');

        // Jika token redirect eksternal tidak lengkap, kembalikan ke halaman depan aplikasi
        if (!$token || !$store) {
            return redirect('/')->with('success', 'Survey berhasil disimpan! Terima kasih.');
        }

        $url = 'https://ess.edpapp.com:2096/form_kunjungan_pelanggan/edit/'
            . $token
            . '?store=' . urlencode($store);

        return redirect()->away($url);
    }
}
