<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasterOutletSurvey;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MasterOutletSurveyExport;

class AdminStatusOutlet extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $role = auth()->user()->role;
        $search = $request->search;
        $status = MasterOutletSurvey::with([
            // 'area.provinsi', 'area.kabupaten.provinsi'



            'kabupaten.provinsi',
            'kabupaten.area'
        ])
            // ->whereBetween('id', [1, 18])
            ->where('status_blast_wa', true)
            ->when($search, function ($query) use ($search) {
                $query->where('nama_outlet', 'like', "%{$search}%")
                    ->orWhere('kode_unik', 'like', "%{$search}%")
                    ->orWhereHas('kabupaten', function ($q) use ($search) {
                        $q->where('nama_kabupaten', 'like', "%{$search}%");
                    });
            })

            ->orderBy('id', 'asc')
            ->paginate(10);


        return view('Admin.admin-status-outlet', compact('status', 'role', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */

    public function export()
    {
        return Excel::download(new MasterOutletSurveyExport, 'status_outlet.xlsx');
    }

    public function EnableAllStatusCode()
    {
        MasterOutletSurvey::where('status_kode_unik', 'N')
            ->update(['status_kode_unik' => 'Y']);
        return redirect()->back()->with('success', 'Semua kode unik berhasil aktif semua');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function DisableAllStatusCode()
    {
        MasterOutletSurvey::where('status_kode_unik', 'Y')
            ->update(['status_kode_unik' => 'N']);
        return redirect()->back()->with('success', 'Semua kode unik berhasil nonaktif semua');
    }

    /**
     * Display the specified resource.
     */
    public function updateStatusCode(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'status' => 'required|in:Y,N',
        ]);

        MasterOutletSurvey::where('id', $request->id)
            ->update(['status_kode_unik' => $request->status]);

        return redirect()->back()->with('success', 'Status berhasil diperbarui');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function RegenerateUnikCode()
    {

        DB::statement("
        UPDATE master_outlet_survey
        SET kode_unik = UPPER(SUBSTRING(REPLACE(UUID(), '-', ''), 1, 10))");

        return redirect()->back()->with('success', 'Kode unik berhasil diregenerasi ulang');
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
