<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasterPertanyaan;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminPdfController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function exportSurveyPdf()
    {
        // Ambil semua pertanyaan per section dengan opsi
        $formUtama = MasterPertanyaan::with('options')
            ->where('master_section_id', 1)
            ->orderBy('order')
            ->get();

        $formHarga = MasterPertanyaan::with('options')
            ->where('master_section_id', 2)
            ->orderBy('order')
            ->get();

        $formKualitas = MasterPertanyaan::with('options')
            ->where('master_section_id', 3)
            ->orderBy('order')
            ->get();

        $formPengiriman = MasterPertanyaan::with('options')
            ->where('master_section_id', 4)
            ->orderBy('order')
            ->get();

        $formPelayanan = MasterPertanyaan::with('options')
            ->where('master_section_id', 5)
            ->orderBy('order')
            ->get();

        // Generate PDF
        $pdf = Pdf::loadView('Admin.Exports.pdf-pertanyaan', compact(
            'formUtama',
            'formHarga',
            'formKualitas',
            'formPengiriman',
            'formPelayanan'
        ))->setPaper('a4', 'portrait');

        return $pdf->download('survey_form.pdf');
    }


    public function index()
    {
         $pertanyaans = MasterPertanyaan::with('options')->orderBy('order')->get();
        return view('Admin.admin-list-pertanyaan', compact('pertanyaans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
