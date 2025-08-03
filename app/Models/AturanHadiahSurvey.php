<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AturanHadiahSurvey extends Model
{
    use HasFactory;

    protected $table = 'aturan_hadiah_survey';
    protected $primaryKey = 'id';
    protected $fillable = [
        'periode_survey_id',
        'provinsi_id',
        'master_kabupaten_id',
        'slot_hadiah_kota',
        'hadiah_id', // foreign key to master_hadiah_survey
        'status_aturan',
    ];

    public function periodeSurvey()
    {
        return $this->belongsTo(PeriodeSurvey::class, 'periode_survey_id', 'id');
    }

    public function provinsi()
    {
        return $this->belongsTo(MasterProvinsi::class, 'provinsi_id', 'id');
    }

    public function kabupaten()
    {
        return $this->belongsTo(MasterKabupaten::class, 'master_kabupaten_id', 'id');
    }

    public function hadiah()
    {
        return $this->belongsTo(MasterHadiah::class, 'hadiah_id', 'id');
    }

}
