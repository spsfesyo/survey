<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use App\Models\AnswerSurvey;
use Illuminate\Http\Request;
use App\Models\MasterProvinsi;
use App\Models\MasterKotaSurvey;
use App\Models\MasterPertanyaan;
use App\Models\MasterRespondent;
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
            'email' => 'required|email'
        ]);

        // Cek apakah email sudah terdaftar
        $existing = MasterRespondent::where('email_respondent', $request->email)->first();

        if ($existing) {
            return redirect()->back()->with('error', 'Email sudah terdaftar!');
        }

        // Simpan jika belum terdaftar
        MasterRespondent::create([
            'email_respondent' => $request->email,
        ]);

        // Simpan ke session
        Session::put('email_respondent', $request->email);

        return redirect()->route('form-utama')->with('success', 'Email berhasil didaftarkan!');
    }

    public function getFormUtama()
    {
        // Cek apakah email sudah terdaftar
        // Ambil data provinsi dan kota

        $provinsi = MasterProvinsi::all();
        $kota = MasterKotaSurvey::all();
        $pertanyaanFormUtama = MasterPertanyaan::with(['tipePertanyaan', 'options'])
            ->where('master_section_id', 1)
            ->orderBy('order')
            ->get();

        $merekBataRingan = MasterJenisPertanyaan::all();
        // $pertanyaans = MasterPertanyaan::with('options')->where('master_section_id',1)->orderBy('order')->get();


        // dd($pertanyaans);
        // return view('form_utama', compact('pertanyaanFormUtama'));

        return view('form-utama', compact('provinsi', 'kota', 'pertanyaanFormUtama', 'merekBataRingan'));
    }



    public function answerFormUtama(Request $request)
    {
        $validated = $request->all();


        session(['form_utama' => $validated]);

        return redirect()->route('form-pertanyaan-kualitas');
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
        // dd($request->foto_base64);
        $validated = $request->all();
        session(['form_pelayanan' => $validated]);


        // dd([
        //     'form_utama' => session('form_utama'),
        //     'foto_base64' => session('form_utama')['foto_base64'] ?? null
        // ]);


        // Ambil semua jawaban dari session
        $formUtama      = session('form_utama');
        $formKualitas   = session('form_kualitas', []);
        $formHarga      = session('form_harga', []);
        $formPengiriman = session('form_pengiriman', []);
        $formPelayanan  = session('form_pelayanan', []);

        // Ambil email dari SESSION
        $email = session('email_respondent');

        if (!$email) {
            return redirect()->back()->with('error', 'Email respondent tidak ditemukan di sesi. Harap ulangi pendaftaran.');
        }

        // Ambil base64 dari REQUEST LANGSUNG
        $fotoBase64 = $request->input('foto_base64');
        $fotoPath = null;

        if (!empty($fotoBase64)) {
            try {
                $base64 = preg_replace('#^data:image/\w+;base64,#i', '', $fotoBase64);
                $imageData = base64_decode($base64);
                $fileName = 'foto-respondent/' . Str::uuid() . '.jpg';

                Storage::disk('public')->put($fileName, $imageData);
                $fotoPath = 'storage/' . $fileName;
            } catch (\Exception $e) {
                $fotoPath = null;
            }
        }

        // Simpan atau update ke master_respondent
        $respondent = MasterRespondent::updateOrCreate(
            ['email_respondent' => $email],
            [
                'nama_respondent'        => $formUtama['nama_respondent'] ?? null,
                'nama_toko_respondent'   => $formUtama['nama_toko_respondent'] ?? null,
                'provinsi_id'            => $formUtama['provinsi_id'] ?? null,
                'kota_id'                => $formUtama['kota_id'] ?? null,
                'alamat_toko_respondent' => $formUtama['alamat_toko_respondent'] ?? null,
                'telepone_respondent'    => $formUtama['telepone_respondent'] ?? null,
                'jenis_pertanyaan_id'    => $formUtama['jenis_pertanyaan_id'] ?? null,
                'foto_selfie'            => $fotoPath,
            ]
        );

        $respondentId = $respondent->id;

        $semuaJawaban = array_filter(
            array_merge($formUtama, $formKualitas, $formHarga, $formPelayanan, $formPengiriman),
            fn($value) => !is_null($value)
        );

        foreach ($semuaJawaban as $key => $value) {
            if (strpos($key, 'pertanyaan_') !== 0 || strpos($key, 'other_') !== false) continue;

            $pertanyaanId = str_replace('pertanyaan_', '', $key);

            if (is_array($value)) {
                foreach ($value as $optionId) {
                    $isOther = strpos($optionId, 'other_') === 0;
                    AnswerSurvey::create([
                        'master_respondent_id'   => $respondentId,
                        'master_pertanyaan_id'   => $pertanyaanId,
                        'pertanyaan_options_id'  => $isOther ? intval(str_replace('other_', '', $optionId)) : intval($optionId),
                        'jawaban_teks'           => $semuaJawaban["teks_$pertanyaanId"] ?? null,
                        'lainnya'                => $semuaJawaban["pertanyaan_{$pertanyaanId}_$optionId"] ?? null,
                    ]);
                }
            } elseif (!is_null($value)) {
                AnswerSurvey::create([
                    'master_respondent_id'   => $respondentId,
                    'master_pertanyaan_id'   => $pertanyaanId,
                    'pertanyaan_options_id'  => is_numeric($value) ? intval($value) : null,
                    'jawaban_teks'           => is_numeric($value) ? ($semuaJawaban["teks_$pertanyaanId"] ?? null) : $value,
                    'lainnya'                => $semuaJawaban["lainnya_$pertanyaanId"] ?? null,
                ]);
            }
        }

        session()->forget([
            'form_utama',
            'form_kualitas',
            'form_harga',
            'form_pelayanan',
            'form_pengiriman',
            'email_respondent'
        ]);

        return redirect()->route('home')->with('success', 'Terima kasih atas partisipasi Anda!');
    }
}









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
