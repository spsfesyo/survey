<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterPeriode extends Model
{
    use HasFactory;

    protected $table = 'master_periode_survey';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nama_periode',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
    ];

    public function respondents()
    {
        return $this->hasMany(MasterRespondent::class, 'periode_id', 'id');
    }

    //     public function outletSurvey()
    // {
    //     return $this->hasMany(MasterOutletSurvey::class, 'periode_id', 'id');
    // }

       public function plotHadiah()
    {
        return $this->hasMany(PlotHadiahSurvey::class, 'periode_survey_id', 'id');
    }

    public function hadiah()
    {
        return $this->hasMany(MasterHadiah::class, 'periode_survey_id', 'id');
    }
}
