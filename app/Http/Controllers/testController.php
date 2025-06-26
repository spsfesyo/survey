<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class testController extends Controller
{
    public function testKota()
    {
        // Ambil data pertama dari tabel master_kota
        $data = DB::table('master_kota')->where('id', 3)->first();

        // Debug data untuk memastikan koneksi berhasil
        dd($data);  // Ini akan menampilkan data dan menghentikan eksekusi script
    }
}
