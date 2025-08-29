<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasterOutletSurvey;
use Illuminate\Pagination\Paginator;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MasterOutletSurveyExport;

class AdminStatusOutlet extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $status = MasterOutletSurvey::with([
            // 'area.provinsi', 'area.kabupaten.provinsi'

            'kabupaten.provinsi',
            'kabupaten.area'
        ])
            // ->whereBetween('id', [1, 18])
            ->where('status_blast_wa', true)
            ->orderBy('id', 'asc')
            ->paginate(10);


        return view('Admin.admin-status-outlet', compact('status'));
    }

    /**
     * Show the form for creating a new resource.
     */

    public function export()
    {
        return Excel::download(new MasterOutletSurveyExport, 'status_outlet.xlsx');
    }

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
