<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use App\Models\AnswerSurvey;
use Illuminate\Http\Request;
use App\Models\MasterProvinsi;
use App\Models\MasterKabupaten;
use App\Models\MasterKotaSurvey;
use App\Models\MasterPertanyaan;
use App\Models\MasterRespondent;
use App\Models\MasterOutletSurvey;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\MasterJenisPertanyaan;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class RespondentController extends Controller
{

    public function index()
    {
        return view('home');
    }


    public function create(Request $request)
    {
        $request->validate([
            'kode_unik' => 'required|string|size:10'
        ]);

        $outlet = MasterOutletSurvey::where('kode_unik', $request->kode_unik)
            ->where('status_kode_unik', 'Y')
            ->first();

        if (!$outlet) {
            return redirect()->back()->with('error', 'Kode unik tidak valid atau sudah digunakan.');
        }

        Session::put('master_outlet_survey_id', $outlet->id);
        Session::put('kode_unik', $request->kode_unik);

        return redirect()->route('form-utama')->with('success', 'Kode diterima. Silakan isi form.');
    }

    public function getFormUtama()
    {
        // Cek apakah email sudah terdaftar
        // Ambil data provinsi dan kota

        $provinsi = MasterProvinsi::all();
        // $kota = MasterKotaSurvey::all();
        $pertanyaanFormUtama = MasterPertanyaan::with(['tipePertanyaan', 'options'])
            ->where('master_section_id', 1)
            ->orderBy('order')
            ->get();

        $merekBataRingan = MasterJenisPertanyaan::all();
        // $pertanyaans = MasterPertanyaan::with('options')->where('master_section_id',1)->orderBy('order')->get();


        // dd($pertanyaans);
        // return view('form_utama', compact('pertanyaanFormUtama'));

        return view('form-utama', compact('provinsi',  'pertanyaanFormUtama', 'merekBataRingan'));
    }



    public function answerFormUtama(Request $request)
    {
        $request->validate([
            'telepone_respondent' => 'required|digits_between:10,15',
            // validasi lainnya jika perlu
        ]);

        $telp = $request->input('telepone_respondent');

        // Cek duplikasi nomor di database
        if (\App\Models\MasterRespondent::where('telepone_respondent', $telp)->exists()) {
            return back()
                ->withInput()
                ->with('phone_duplicate', $telp);
        }

        // Simpan session jika valid
        session(['form_utama' => $request->all()]);

        return redirect()->route('form-pertanyaan-kualitas');
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
    // public function answerFormPelayanan(Request $request)
    // {
    //     $validated = $request->all();
    //     session(['form_pelayanan' => $validated]);
    //     // return redirect()->route('form-pertanyaan-pelayanan');
    // }
    public function submitFinalAnswer(Request $request)
    {
        $formUtama = session('form_utama', []);
        $phone = $formUtama['telepone_respondent'] ?? null;
        if ($phone && MasterRespondent::where('telepone_respondent', $phone)->exists()) {
            return redirect()
                ->route('form-pertanyaan-pelayanan')  // route menuju form terakhir
                ->with('phone_duplicate', $phone)
                ->withInput();
        }

        // 1️⃣ Simpan sesi form pelayanan
        session(['form_pelayanan' => $request->all()]);

        // 2️⃣ Ambil semua sesi form
        $formUtama      = session('form_utama', []);
        $formKualitas   = session('form_kualitas', []);
        $formHarga      = session('form_harga', []);
        $formPengiriman = session('form_pengiriman', []);
        $formPelayanan  = session('form_pelayanan', []);

        // 3️⃣ Cek duplikat no. telepon sebelum eksekusi lainnya

        // 4️⃣ Ambil sesi kode unik & outlet
        $outletId = session('master_outlet_survey_id');
        $kodeUnik = session('kode_unik');
        if (!$outletId || !$kodeUnik || empty($formUtama)) {
            return redirect()->route('home')->with('error', 'Sesi tidak lengkap. Silakan ulangi.');
        }

        // 5️⃣ Proses foto (base64)
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

        // 6️⃣ Ambil provinsi & kabupaten
        $provId = $formUtama['provinsi'] ?? null;
        $kabId  = $formUtama['kabupaten'] ?? null;

        // 7️⃣ Logika slot/responden berdasarkan kabupaten
        if ($kabId) {
            $slot = MasterRespondent::where('master_kabupaten_id', $kabId)
                ->whereNull('master_outlet_survey_id')
                ->orderBy('id')
                ->first();

            if ($slot) {
                $respondent = $slot;
                $respondent->update([
                    'master_outlet_survey_id'  => $outletId,
                    'telepone_respondent'      => $phone,
                    'provinsi_id'              => $provId,
                    'master_kabupaten_id'      => $kabId,
                    'nama_respondent'          => $formUtama['nama_respondent'] ?? null,
                    'nama_toko_respondent'     => $formUtama['nama_toko_respondent'] ?? null,
                    'alamat_toko_respondent'   => $formUtama['alamat_toko_respondent'] ?? null,
                    'foto_selfie'              => $fotoPath,
                ]);
            } else {
                $respondent = MasterRespondent::create([
                    'master_outlet_survey_id'  => $outletId,
                    'master_kabupaten_id'      => $kabId,
                    'telepone_respondent'      => $phone,
                    'provinsi_id'              => $provId,
                    'nama_respondent'          => $formUtama['nama_respondent'] ?? null,
                    'nama_toko_respondent'     => $formUtama['nama_toko_respondent'] ?? null,
                    'alamat_toko_respondent'   => $formUtama['alamat_toko_respondent'] ?? null,
                    'foto_selfie'              => $fotoPath,
                ]);
            }
        } else {
            $respondent = MasterRespondent::create([
                'master_outlet_survey_id' => $outletId,
                'master_kabupaten_id'     => null,
                'telepone_respondent'     => $phone,
                'provinsi_id'             => $provId,
                'nama_respondent'         => $formUtama['nama_respondent'] ?? null,
                'nama_toko_respondent'    => $formUtama['nama_toko_respondent'] ?? null,
                'alamat_toko_respondent'  => $formUtama['alamat_toko_respondent'] ?? null,
                'foto_selfie'             => $fotoPath,
            ]);
        }

        $respondentId = $respondent->id;

        // 8️⃣ Simpan jawaban ke AnswerSurvey
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

        // 9️⃣ Update kode unik outlet jadi 'N'
        MasterOutletSurvey::where('id', $outletId)
            ->update(['status_kode_unik' => 'N']);

        // 🔟 Ambil nama hadiah untuk SweetAlert
        $hadiahNama = optional($respondent->hadiah)->nama_hadiah;

        // 1️⃣1️⃣ Bersihkan sesi
        session()->forget(['form_utama', 'form_kualitas', 'form_harga', 'form_pengiriman', 'form_pelayanan', 'master_outlet_survey_id', 'kode_unik']);

        // 1️⃣2️⃣ Redirect dengan SweetAlert
        if ($hadiahNama) {
            return redirect()->route('home')->with('success', "Selamat! Anda mendapatkan hadiah: $hadiahNama");
        }
        return redirect()->route('home')->with('success', 'Terima kasih atas partisipasi Anda!, Maaf anda belum beruntung memenangkan hadiah menarik dari kami.');
    }

    public function checkDuplicatePhone(Request $request)
    {
        $phone = $request->query('phone');
        $exists = MasterRespondent::where('telepone_respondent', $phone)->exists();
        return response()->json(['exists' => $exists]);
    }
}










//  public function submitFinalAnswer(Request $request)
//     {
//         // dd($request->foto_base64);
//         $validated = $request->all();
//         session(['form_pelayanan' => $validated]);


//         // dd([
//         //     'form_utama' => session('form_utama'),
//         //     'foto_base64' => session('form_utama')['foto_base64'] ?? null
//         // ]);


//         // Ambil semua jawaban dari session
//         $formUtama      = session('form_utama');
//         $formKualitas   = session('form_kualitas', []);
//         $formHarga      = session('form_harga', []);
//         $formPengiriman = session('form_pengiriman', []);
//         $formPelayanan  = session('form_pelayanan', []);

//         // Ambil email dari SESSION
//         $email = session('email_respondent');

//         if (!$email) {
//             return redirect()->back()->with('error', 'Email respondent tidak ditemukan di sesi. Harap ulangi pendaftaran.');
//         }

//         // Ambil base64 dari REQUEST LANGSUNG
//         $fotoBase64 = $request->input('foto_base64');
//         $fotoPath = null;

//         if (!empty($fotoBase64)) {
//             try {
//                 $base64 = preg_replace('#^data:image/\w+;base64,#i', '', $fotoBase64);
//                 $imageData = base64_decode($base64);
//                 $fileName = 'foto-respondent/' . Str::uuid() . '.jpg';

//                 Storage::disk('public')->put($fileName, $imageData);
//                 $fotoPath = 'storage/' . $fileName;
//             } catch (\Exception $e) {
//                 $fotoPath = null;
//             }
//         }

//         // Simpan atau update ke master_respondent
//         $respondent = MasterRespondent::updateOrCreate(
//             ['email_respondent' => $email],
//             [
//                 'nama_respondent'        => $formUtama['nama_respondent'] ?? null,
//                 'nama_toko_respondent'   => $formUtama['nama_toko_respondent'] ?? null,
//                 'provinsi_id'            => $formUtama['provinsi_id'] ?? null,
//                 'kota_id'                => $formUtama['kota_id'] ?? null,
//                 'alamat_toko_respondent' => $formUtama['alamat_toko_respondent'] ?? null,
//                 'telepone_respondent'    => $formUtama['telepone_respondent'] ?? null,
//                 'jenis_pertanyaan_id'    => $formUtama['jenis_pertanyaan_id'] ?? null,
//                 'foto_selfie'            => $fotoPath,
//             ]
//         );

//         $respondentId = $respondent->id;

//         $semuaJawaban = array_filter(
//             array_merge($formUtama, $formKualitas, $formHarga, $formPelayanan, $formPengiriman),
//             fn($value) => !is_null($value)
//         );

//         foreach ($semuaJawaban as $key => $value) {
//             if (strpos($key, 'pertanyaan_') !== 0 || strpos($key, 'other_') !== false) continue;

//             $pertanyaanId = str_replace('pertanyaan_', '', $key);

//             if (is_array($value)) {
//                 foreach ($value as $optionId) {
//                     $isOther = strpos($optionId, 'other_') === 0;
//                     AnswerSurvey::create([
//                         'master_respondent_id'   => $respondentId,
//                         'master_pertanyaan_id'   => $pertanyaanId,
//                         'pertanyaan_options_id'  => $isOther ? intval(str_replace('other_', '', $optionId)) : intval($optionId),
//                         'jawaban_teks'           => $semuaJawaban["teks_$pertanyaanId"] ?? null,
//                         'lainnya'                => $semuaJawaban["pertanyaan_{$pertanyaanId}_$optionId"] ?? null,
//                     ]);
//                 }
//             } elseif (!is_null($value)) {
//                 AnswerSurvey::create([
//                     'master_respondent_id'   => $respondentId,
//                     'master_pertanyaan_id'   => $pertanyaanId,
//                     'pertanyaan_options_id'  => is_numeric($value) ? intval($value) : null,
//                     'jawaban_teks'           => is_numeric($value) ? ($semuaJawaban["teks_$pertanyaanId"] ?? null) : $value,
//                     'lainnya'                => $semuaJawaban["lainnya_$pertanyaanId"] ?? null,
//                 ]);
//             }
//         }

//         session()->forget([
//             'form_utama',
//             'form_kualitas',
//             'form_harga',
//             'form_pelayanan',
//             'form_pengiriman',
//             'email_respondent'
//         ]);

//         return redirect()->route('home')->with('success', 'Terima kasih atas partisipasi Anda!');
//     }









        // dd([
        //     'form_utama' => session('form_utama'),
        //     'foto_base64' => session('form_utama')['foto_base64'] ?? null
        // ]);






// $validated = $request->all();
// session(['form_pelayanan' => $validated]);

// // Ambil semua jawaban dari session
// $formUtama      = session('form_utama');
// $formKualitas   = session('form_kualitas', []);
// $formHarga      = session('form_harga', []);
// $formPengiriman = session('form_pengiriman', []);
// $formPelayanan  = session('form_pelayanan', []);
// $email          = $request->input('email_respondent');

// // Simpan ke tabel master_respondent
// $respondent = MasterRespondent::updateOrCreate(
//     ['email_respondent' => $email],
//     [
//         'nama_respondent'        => $formUtama['nama_respondent'],
//         'nama_toko_respondent'   => $formUtama['nama_toko_respondent'],
//         'provinsi_id'            => $formUtama['provinsi_id'],
//         'kota_id'                => $formUtama['kota_id'],
//         'alamat_toko_respondent' => $formUtama['alamat_toko_respondent'],
//         'telepone_respondent'     => $formUtama['telepone_respondent'],
//         'jenis_pertanyaan_id'    => $formUtama['jenis_pertanyaan_id'],
//         'email_respondent'       => $email,
//     ]
// );

// $respondentId = $respondent->id;

// // Gabungkan semua form dan filter null value
// $semuaJawaban = array_filter(
//     array_merge($formKualitas, $formHarga, $formPelayanan, $formPengiriman),
//     fn($value) => !is_null($value)
// );

// foreach ($semuaJawaban as $key => $value) {
//     if (strpos($key, 'pertanyaan_') === 0) {
//         $pertanyaanId = str_replace('pertanyaan_', '', $key);

//         if (is_array($value)) {
//             foreach ($value as $optionId) {
//                 // Pastikan hanya angka valid yang dikirim
//                 if (!is_numeric($optionId)) continue;

//                 AnswerSurvey::create([
//                     'master_respondent_id'   => $respondentId,
//                     'master_pertanyaan_id'   => $pertanyaanId,
//                     'pertanyaan_options_id'  => intval($optionId),
//                     'jawaban_teks'           => $semuaJawaban["teks_$pertanyaanId"] ?? null,
//                     'lainnya'                => $semuaJawaban["lainnya_$pertanyaanId"] ?? null,
//                 ]);
//             }
//         } elseif (!is_null($value)) {
//             // Jika single value (radio button)
//             if (!is_numeric($value)) continue;

//             AnswerSurvey::create([
//                 'master_respondent_id'   => $respondentId,
//                 'master_pertanyaan_id'   => $pertanyaanId,
//                 'pertanyaan_options_id'  => intval($value),
//                 'jawaban_teks'           => $semuaJawaban["teks_$pertanyaanId"] ?? null,
//                 'lainnya'                => $semuaJawaban["lainnya_$pertanyaanId"] ?? null,
//             ]);
//         }
//     }
// }
// // Kosongkan session
// session()->forget(['form_utama', 'form_kualitas', 'form_harga', 'form_pelayanan', 'form_pengiriman']);

// return redirect()->route('home')->with('success', 'Terima kasih atas partisipasi Anda!');
