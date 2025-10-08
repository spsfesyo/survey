<?php

namespace App\Exports;

use App\Models\MasterPertanyaan;
use App\Models\MasterRespondent;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class RespondentExport implements FromView
{

    protected $jenisId;

    public function __construct($jenisId)
    {
        $this->jenisId = $jenisId;
    }

    public function view(): View
    {
        $respondents = MasterRespondent::with(['provinsi', 'kabupaten', 'JenisPertanyaan', 'answers.options'])
            ->where('jenis_pertanyaan_id', $this->jenisId)
            ->orderBy('created_at', 'asc')
            ->get();

        $pertanyaanList = MasterPertanyaan::orderBy('order')->get();

        // Ubah data foto jadi link penuh agar bisa diakses lewat Excel
        foreach ($respondents as $respondent) {
            if ($respondent->foto_selfie) {
                // Buat URL publik untuk foto respondent
                $respondent->foto_selfie_url = url( $respondent->foto_selfie);
            } else {
                $respondent->foto_selfie_url = '-';
            }
        }

        return view('Admin.Exports.excel', compact('respondents', 'pertanyaanList'));
    }
}

    // public function drawings()
    // {
    //     $drawings = [];

    //     // Ambil semua respondent sesuai jenis pertanyaan
    //     $respondents = MasterRespondent::where('jenis_pertanyaan_id', $this->jenisId)
    //         ->orderBy('created_at', 'asc')
    //         ->get();

    //     // Loop untuk setiap respondent
    //     foreach ($respondents as $index => $respondent) {
    //         if ($respondent->foto_selfie) {
    //             $path = public_path('storage/foto-respondent/' . $respondent->foto_selfie);

    //             if (file_exists($path)) {
    //                 $drawing = new Drawing();
    //                 $drawing->setName('Foto Respondent');
    //                 $drawing->setDescription('Foto Selfie');
    //                 $drawing->setPath($path);

    //                 // ✅ Atur ukuran gambar seragam
    //                 $drawing->setWidth(70);   // lebar gambar
    //                 $drawing->setHeight(70);  // tinggi gambar

    //                 // ✅ Posisi gambar di kolom G, baris menyesuaikan data
    //                 //   (ubah 'G' jika kolom kamu berbeda)
    //                 $drawing->setCoordinates('G' . ($index + 2));

    //                 // ✅ Atur offset agar posisi gambar tidak terlalu mepet
    //                 $drawing->setOffsetX(5);
    //                 $drawing->setOffsetY(5);

    //                 $drawings[] = $drawing;
    //             }
    //         }
    //     }

    //     return $drawings;
    // }




// <?php

// namespace App\Exports;

// use App\Models\MasterRespondent;
// use App\Models\MasterPertanyaan;
// use Maatwebsite\Excel\Concerns\FromView;
// use Illuminate\Contracts\View\View;

// class RespondentExport implements FromView
// {

//     protected $jenisId;

//     public function __construct($jenisId)
//     {
//         $this->jenisId = $jenisId;
//     }

//     public function view(): View
//     {
//         $respondents = MasterRespondent::with(['provinsi', 'kabupaten', 'JenisPertanyaan', 'answers.options'])
//             ->where('jenis_pertanyaan_id', $this->jenisId)
//             ->orderBy('created_at', 'asc')
//             ->get();

//         $pertanyaanList = MasterPertanyaan::orderBy('order')->get();

//         return view('Admin.Exports.excel', compact('respondents', 'pertanyaanList'));
//     }
// }
