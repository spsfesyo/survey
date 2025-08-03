<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriodeSurvey extends Model
{
    use HasFactory;
    protected $table = 'periode_survey'; // nama tabel
    protected $primaryKey = 'id'; // nama primary key
    protected $fillable = [
        'nama_periode', // kolom yang dapat diisi
        'start_at', // kolom yang dapat diisi
        'end_at', // kolom yang dapat diisi
        'status', // kolom yang dapat diisi
    ];

    public function respondents()
    {
        return $this->hasMany(MasterRespondent::class, 'periode_survey_id', 'id');
    }
    public function aturanHadiah()
    {
        return $this->hasMany(AturanHadiahSurvey::class, 'periode_survey_id', 'id');
    }
    public function plotHadiah()
    {
        return $this->hasMany(PlotHadiahSurvey::class, 'periode_survey_id', 'id');
    }
    public function historyPemenang()
    {
        return $this->hasMany(HistoryPemenangSurvey::class, 'periode_survey_id', 'id');
    }
}
