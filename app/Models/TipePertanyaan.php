<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipePertanyaan extends Model
{
    use HasFactory;
    protected $table = 'master_tipe_pertanyaan'; // nama tabel
    protected $primaryKey = 'id'; // nama primary key
    protected $fillable = [

        'tipe_pertanyaan',
    ]; // kolom yang dapat diisi

    public function pertanyaan()
    {
        return $this->hasMany(MasterPertanyaan::class, 'master_tipe_pertanyaan_id');
    }

}
