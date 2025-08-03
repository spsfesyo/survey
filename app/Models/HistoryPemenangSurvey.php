<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryPemenangSurvey extends Model
{
    use HasFactory;

    protected $table = 'history_pemenang_survey';
    protected $primaryKey = 'id';

    protected $fillable = [
        'periode_survey_id',
        'mater_outlet_survey_id', // ikut nama kolom di migration (typo)
        'master_kabupaten_id',
        'hadiah_id',
        'status_history',
    ];
    public function periodeSurvey()
    {
        return $this->belongsTo(PeriodeSurvey::class, 'periode_survey_id');
    }
    public function masterOutletSurvey()
    {
        return $this->belongsTo(MasterOutletSurvey::class, 'mater_outlet_survey_id'); // ikut nama kolom di migration (typo)
    }
    public function kabupaten()
    {
        return $this->belongsTo(MasterKabupaten::class, 'master_kabupaten_id');
    }
    public function hadiah()
    {
        return $this->belongsTo(MasterHadiah::class, 'hadiah_id');
    }
}
