<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckBlast extends Model
{
    use HasFactory;

    protected $table = 'checks'; // sesuaikan jika nama tabel bukan 'check_blasts'

    protected $fillable = [
        'nama_check',
        'no_telp_check',
        'kode_unik',
        'status_blast_wa',
    ];
}
