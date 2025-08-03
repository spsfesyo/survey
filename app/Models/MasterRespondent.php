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
        'periode_survey_id',
        'master_outlet_survey_id',
        'nama_respondent',
        'email_respondent',
        'telepone_respondent',
        'nama_toko_respondent',
        'alamat_toko_respondent',
        'foto_selfie',
        'provinsi_id',
        'master_kabupaten_id',
        'jenis_pertanyaan_id',
        'hadiah_id',
        'periode_id', // foreign key to PeriodeSurvey
        'status_hadiah'

    ];


    public function provinsi()
    {
        return $this->belongsTo(MasterProvinsi::class, 'provinsi_id');
    }

    public function kabupaten()
    {
        return $this->belongsTo(MasterKabupaten::class, 'master_kabupaten_id');
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
    public function outletSurvey()
    {
        return $this->belongsTo(MasterOutletSurvey::class, 'master_outlet_survey_id');
    }
    public function hadiah()
    {
        return $this->belongsTo(MasterHadiah::class, 'hadiah_id');
    }
    public function periodeSurvey()
    {
        return $this->belongsTo(PeriodeSurvey::class, 'periode_survey_id');
    }
}
