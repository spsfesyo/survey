<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterOutletSurvey extends Model
{
    use HasFactory;
    protected $table = 'master_outlet_survey';
    protected $primaryKey = 'id';
    protected $fillable = [
        'master_kabupaten_id',
        'nama_outlet',
        'sps_internal_name',
        'telepone_outlet',
        'kode_unik',
        'status_kode_unik',
        'periode', // Periode survey, nullable
    ];

    public function kabupaten()
    {
        return $this->belongsTo(MasterKabupaten::class, 'master_kabupaten_id', 'id');
    }


    public function respondents()
    {
        return $this->hasMany(MasterRespondent::class, 'master_outlet_survey_id');
    }

    public function plotHadiah()
    {
        return $this->hasMany(PlotHadiahSurvey::class, 'master_outlet_survey_id', 'id');
    }
    public function historyPemenang()
    {
        return $this->hasMany(HistoryPemenangSurvey::class, 'mater_outlet_survey_id', 'id'); // ikut nama kolom di migration (typo)
    }

}
