<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\testController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\RespondentController;
use App\Http\Controllers\AdminStatistikController;
use App\Http\Controllers\DoorprizeController;

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

Route::get('/', [RespondentController::class, 'index'])->name('home');

Route::post('/submit-kode-unik', [RespondentController::class, 'create'])->name('submit-kode-unik');

Route::get('/form-utama', [RespondentController::class, 'getFormUtama'])->name('form-utama');
Route::post('/form-utama', [RespondentController::class, 'answerFormUtama'])->name('post-form-utama');
Route::get('/get-kabupaten/{provinsi_id}', [RespondentController::class, 'getKabupatenByProvinsi'])->name('get-kabupaten');



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
Route::post('/form-pertanyaan-pelayanan', [RespondentController::class, 'submitFinalAnswer'])->name('post-form-pertanyaan-pelayanan');



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
Route::middleware(['auth'])->group(function () {

    Route::get('/admin-dashboard', [AdminAuthController::class, 'showDashboard'])->name('admin-dashboard');
    Route::get('/admin-statistik', [AdminStatistikController::class, 'showStatistik'])->name('admin-statistik');
    // Route::get('/admin-statistik', [AdminStatistikController::class, 'showTableKuisioner'])->name('admin-statistik-table');
    // Route::get('/admin-statistik', [AdminStatistikController::class, 'showPieChart'])->name('admin-pie-chart');
    Route::get('/download-charts/{type}', [AdminStatistikController::class, 'downloadChartsZip'])->name('download.charts.zip');
    Route::get('/admin/statistik/pie-data', [AdminStatistikController::class, 'downloadPieCharts'])->name('download-pie-charts');
    Route::get('/export-respondent/{jenisId}', [AdminStatistikController::class, 'ExportExcel'])->name('export.respondent');
    Route::get('/admin-doorprize', [DoorprizeController::class, 'index'])->name('admin-doorprize');
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
