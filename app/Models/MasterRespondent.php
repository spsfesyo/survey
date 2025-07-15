<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterRespondent extends Model
{
    use HasFactory;
    protected $table = 'master_respondent';
    protected $primaryKey = 'id';
    protected $fillable = [

        'nama_respondent',
        'email_respondent',
        'telepone_respondent',
        'nama_toko_respondent',
        'alamat_toko_respondent',
        'foto_selfie',
        'provinsi_id',
        'kabupaten_id',
        'jenis_pertanyaan_id'

    ];


    public function provinsi()
    {
        return $this->belongsTo(MasterProvinsi::class, 'provinsi_id');
    }

    public function kabupaten()
    {
        return $this->belongsTo(MasterKotaSurvey::class, 'kabupaten_id');
    }

    public function jenisPertanyaan()
    {
        return $this->belongsTo(MasterJenisPertanyaan::class, 'jenis_pertanyaan_id');
    }

    public function answers()
    {
        return $this->hasMany(AnswerSurvey::class, 'master_respondent_id');
    }

    public function pertanyaan()
    {
        return $this->hasMany(MasterPertanyaan::class, 'master_respondent_id');
    }
}
