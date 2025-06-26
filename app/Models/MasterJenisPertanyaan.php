<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterJenisPertanyaan extends Model
{
    use HasFactory;
    protected $table = 'master_jenis_pertanyaan';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'jenis_pertanyaan',

    ];

    public function pertanyaan()
    {
        return $this->hasMany(MasterPertanyaan::class, 'master_jenis_pertanyaan_id');
    }

    public function respondents()
    {
        return $this->hasMany(MasterRespondent::class, 'jenis_pertanyaan_id');
    }
}
