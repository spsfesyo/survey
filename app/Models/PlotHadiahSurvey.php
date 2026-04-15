<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlotHadiahSurvey extends Model
{
    use HasFactory;
    protected $table = 'plot_hadiah_survey';
    protected $primaryKey = 'id';
    protected $fillable = [
        'periode_survey_id',
        'provinsi_id',
        'master_kabupaten_id',
        'master_area_id',
        'master_outlet_survey_id',
        'hadiah_id',
        'is_winning',
        'tanggal_menang',
        'status_respondent_assigned',
        'respondent_id',
    ];

    public function periode()
    {
        return $this->belongsTo(MasterPeriode::class, 'periode_survey_id', 'id');
    }
    public function provinsi()
    {
        return $this->belongsTo(MasterProvinsi::class, 'provinsi_id', 'id');
    }
    public function kabupaten()
    {
        return $this->belongsTo(MasterKabupaten::class, 'master_kabupaten_id', 'id');
    }
    public function outletSurvey()
    {
        return $this->belongsTo(MasterOutletSurvey::class, 'master_outlet_survey_id', 'id');

    }
    public function hadiah()
    {
        return $this->belongsTo(MasterHadiah::class, 'hadiah_id', 'id');
    }
    public function respondents()
    {
        return $this->hasMany(MasterRespondent::class, 'respondent_id', 'id');
    }
    public function area()
    {
        return $this->belongsTo(MasterAreaSurvey::class, 'master_area_id', 'id');
    }
}
