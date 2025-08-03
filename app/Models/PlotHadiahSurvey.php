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
        'master_outlet_survey_id',
        'hadiah_id',
        'status_plot',
        'status_respondent_assigned',
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
    public function outletSurvey()
    {
        return $this->belongsTo(MasterOutletSurvey::class, 'master_outlet_survey_id', 'id');

    }
    public function hadiah()
    {
        return $this->belongsTo(MasterHadiah::class, 'hadiah_id', 'id');
    }
    // public function respondents()
    // {
    //     return $this->hasMany(MasterRespondent::class, 'plot_hadiah_survey_id', 'id');
    // }
}
