<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminBlastController;
use App\Http\Controllers\AdminBlastManual;
use App\Http\Controllers\AdminKonfigController;
use App\Http\Controllers\AdminPdfController;
use App\Http\Controllers\AdminPlotController;
use App\Http\Controllers\AdminReportController;
use App\Http\Controllers\AdminStatistikController;
use App\Http\Controllers\AdminStatusOutlet;
use App\Http\Controllers\DoorprizeController;
use App\Http\Controllers\RespondentController;
use App\Http\Controllers\testController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('home');
// });

// index awal
Route::get('/', [RespondentController::class, 'getFormUtama'])->name('home');

// Route::post('/submit-kode-unik', [RespondentController::class, 'create'])->name('submit-kode-unik');

Route::get('/form-utama', [RespondentController::class, 'getFormUtama'])->name('form-utama');
Route::post('/form-utama', [RespondentController::class, 'answerFormUtama'])->name('post-form-utama');
Route::get('/get-kabupaten/{provinsi_id}', [RespondentController::class, 'getKabupatenByProvinsi'])->name('get-kabupaten');
// Route::get('/check-phone-duplicate', [RespondentController::class, 'checkDuplicatePhone'])->name('check-duplicate-phone');




// Route::get('/form-utama',function () {
//     return view('form-utama');
// })->name('form-utama');

// Route::get('/form-pertanyaan-kualitas',function () {
//     return view('form-pertanyaan-kualitas');
// });

Route::get('/form-pertanyaan-kualitas', [RespondentController::class, 'getFormKualitas'])->name('form-pertanyaan-kualitas');
Route::post('/form-pertanyaan-kualitas', [RespondentController::class, 'answerFormKualitas'])->name('post-form-pertanyaan-kualitas');
Route::get('/form-pertanyaan-harga', [RespondentController::class, 'getFormHarga'])->name('form-pertanyaan-harga');
Route::post('/form-pertanyaan-harga', [RespondentController::class, 'answerFormHarga'])->name('post-form-pertanyaan-harga');
Route::get('/form-pertanyaan-pengiriman', [RespondentController::class, 'getFormPengiriman'])->name('form-pertanyaan-pengiriman');
Route::post('/form-pertanyaan-pengiriman', [RespondentController::class, 'answerFormPengiriman'])->name('post-form-pertanyaan-pengiriman');
Route::get('/form-pertanyaan-pelayanan', [RespondentController::class, 'getFormPelayanan'])->name('form-pertanyaan-pelayanan');
Route::post('/form-pertanyaan-pelayanan', [RespondentController::class, 'answerFormPelayanan'])->name('post-form-pertanyaan-pelayanan');
Route::get('/form-pertanyaan-gimmick', [RespondentController::class, 'getFormGimmick'])->name('form-pertanyaan-gimmick');
Route::post('/form-pertanyaan-gimmick', [RespondentController::class, 'answerFormGimmick'])->name('post-form-pertanyaan-gimmick');

Route::post('/submit-final', [RespondentController::class, 'submitFinalAnswer'])->name('submit-final');






// Route::get('/form-pertanyaan-harga',function () {
//     return view('form-pertanyaan-harga');
// });

// Route::get('/form-pertanyaan-pengiriman',function () {
//     return view('form-pertanyaan-pengiriman');
// });

// Route::get('/form-pertanyaan-pelayanan',function () {
//     return view('form-pertanyaan-pelayanan');
// });

Route::get('/auth-admin-survey', [AdminAuthController::class, 'showLogin'])->name('login');
Route::post('/auth-admin-survey', [AdminAuthController::class, 'login']);
Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
Route::middleware(['auth', 'role:1,2'])->group(function () {

    Route::get('/admin-dashboard', [AdminAuthController::class, 'showDashboard'])->name('admin-dashboard');
    Route::get('/admin-statistik', [AdminStatistikController::class, 'showStatistik'])->name('admin-statistik');
    // Route::get('/admin-statistik', [AdminStatistikController::class, 'showTableKuisioner'])->name('admin-statistik-table');
    // Route::get('/admin-statistik', [AdminStatistikController::class, 'showPieChart'])->name('admin-pie-chart');
    Route::get('/download-charts/{type}', [AdminStatistikController::class, 'downloadChartsZip'])->name('download.charts.zip');
    Route::get('/admin/statistik/pie-data', [AdminStatistikController::class, 'downloadPieCharts'])->name('download-pie-charts');
    Route::get('/export-respondent/{jenisId}', [AdminStatistikController::class, 'ExportExcel'])->name('export.respondent');
    //Route::get('/admin-doorprize', [DoorprizeController::class, 'index'])->name('admin-doorprize');
    Route::get('/admin-status-outlet', [AdminStatusOutlet::class, 'index'])->name('admin-status-outlet');
    Route::get('/export-status-outlet', [AdminStatusOutlet::class, 'export'])->name('status.export');
    Route::get('/admin-list-pertanyaan', [AdminPdfController::class, 'index'])->name('admin-list-pertanyaan');
    Route::get('/export-survey', [AdminPdfController::class, 'exportSurveyPdf'])->name('export-survey-pdf');

    // Konfigurasi Survey
    Route::get('/konfig-survey', [AdminKonfigController::class, 'index'])->name('konfig-survey');
    //konfig periode
    Route::post('/periode/store', [AdminKonfigController::class, 'PeriodeCreate'])->name('periode-create');
    Route::put('/periode/update', [AdminKonfigController::class, 'PeriodeUpdate'])->name('periode-update');
    //konfig hadiah
    Route::post('/hadiah/store', [AdminKonfigController::class, 'HadiahCreate'])->name('hadiah-create');
    Route::put('/hadiah/update', [AdminKonfigController::class, 'HadiahUpdate'])->name('hadiah-update');
    //konfig plot hadiah
    Route::post('plot-hadiah/store', [AdminKonfigController::class, 'PlotCreate'])->name('plot-store');
    Route::put('/plot/{id}/update', [AdminKonfigController::class, 'updatePlot'])
        ->name('plot-update');
    Route::delete('/plot/{id}/delete', [AdminKonfigController::class, 'deletePlot'])
        ->name('plot.delete');


    // status otulet
    Route::post('/enable-kode-unik', [AdminStatusOutlet::class, 'EnableAllStatusCode'])->name('enable-kode-unik');
    Route::post('/disable-kode-unik', [AdminStatusOutlet::class, 'DisableAllStatusCode'])->name('disable-kode-unik');
    Route::post('/outlet-status/update', [AdminStatusOutlet::class, 'updateStatusCode'])->name('outlet-status-kode-update');
    Route::post('/outlet-status/regenerate', [AdminStatusOutlet::class, 'RegenerateUnikCode'])->name('outlet-regenerate-unik-code');


    //report

    //untuk menampilkan index
    Route::get('/admin-report', [AdminReportController::class, 'index'])->name('view-report');
    //untuk get dan submit date picker periode
    Route::get('/admin/report/export/{id}', [AdminReportController::class, 'export'])->name('report.export');
    //untuk export excelnya
    Route::get('/admin/report/export/{id}', [AdminReportController::class, 'export'])->name('admin-report-export');
});

Route::middleware(['auth', 'role:1'])->group(function () {
    Route::get('/admin-blast-wa', [AdminBlastController::class, 'index'])->name('admin-blast-wa');
    Route::post('/admin-blast-wa', [AdminBlastController::class, 'BlastingWa'])->name('admin-blast-wa.post');
    Route::post('/admin-blast-wa/pause', [AdminBlastController::class, 'pauseBlast'])->name('admin-blast.pause');
    Route::post('/admin-blast-wa/resume', [AdminBlastController::class, 'resumeBlast'])->name('admin-blast.resume');
    // Route::get('/admin-list-pertanyaan', [AdminPdfController::class, 'index'])->name('admin-list-pertanyaan');
    // Route::get('/export-survey', [AdminPdfController::class, 'exportSurveyPdf'])->name('export-survey-pdf');


    Route::get('/admin-plot-random', [AdminPlotController::class, 'index'])->name('admin-plot-random');
});

// Route::middleware('auth:survey')->get('/dashboard', function () {
//     $user = Auth::guard('survey')->user();
//     return view('Admin.admin-dashboard', compact('user'));
// })->name('admin-dashboard');



// Route::get('/admin-dashboard',function () {
//     return view('Admin.admin-dashboard');
// })->name('admin-dashboard');

// Route::get('/pie-chart', function () {
//     return view('Admin.Charts.pie',);
// })->name('pie-chart');


// Route::get('/bar-chart', function () {
//     return view('Admin.Charts.bar');
// })->name('bar-chart');
// Route::get('/test-kota', [testController::class, 'testKota']);
