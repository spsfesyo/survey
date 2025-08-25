<?php

namespace App\Exports;

use App\Models\MasterRespondent;
use App\Models\MasterPertanyaan;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

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

        return view('Admin.exports.excel', compact('respondents', 'pertanyaanList'));
    }
}
