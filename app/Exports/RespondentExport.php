<?php

namespace App\Exports;

use App\Models\MasterRespondent;
use App\Models\MasterPertanyaan;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class RespondentExport implements FromView
{
    public function view(): View
    {
        $respondents = MasterRespondent::with([
            'provinsi',
            'kota',
            'jenisPertanyaan',
            'answers.options'
        ])->get();

        $pertanyaanList = MasterPertanyaan::orderBy('order')->get();

        return view('Admin.Exports.excel', compact('respondents', 'pertanyaanList'));
    }
}
