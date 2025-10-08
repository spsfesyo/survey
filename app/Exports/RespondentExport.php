<?php

namespace App\Exports;

use App\Models\MasterPertanyaan;
use App\Models\MasterRespondent;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class RespondentExport implements FromView, WithDrawings
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

        return view('Admin.Exports.excel', compact('respondents', 'pertanyaanList'));
    }

    public function drawings()
    {
        $drawings = [];

        $respondents = MasterRespondent::where('jenis_pertanyaan_id', $this->jenisId)
            ->orderBy('created_at', 'asc')
            ->get();

        foreach ($respondents as $index => $respondent) {
            $path = storage_path('app/public/foto-respondent/' . $respondent->foto_selfie);
            if ($respondent->foto_selfie && file_exists($path)) {
                $drawing = new Drawing();
                $drawing->setName('Foto Respondent');
                $drawing->setDescription('Foto Selfie');
                $drawing->setPath($path);
                $drawing->setHeight(80);
                $drawing->setCoordinates('E' . ($index + 2)); // kolom E sesuai urutan di tabel
                $drawings[] = $drawing;
            }
        }

        return $drawings;
    }
}



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
