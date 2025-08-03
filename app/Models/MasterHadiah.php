<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterHadiah extends Model
{
    use HasFactory;
    protected $table = 'master_hadiah_survey';
    protected $fillable = [
        'kode_hadiah',
        'nama_hadiah',
        'jumlah_hadiah',
        'status',

    ];

    public function respondents()
    {
        return $this->hasMany(MasterRespondent::class, 'hadiah_id');
    }

    public function aturanHadiah()
    {
        return $this->hasMany(AturanHadiahSurvey::class, 'hadiah_id');
    }
    public function plotHadiah()
    {
        return $this->hasMany(PlotHadiahSurvey::class, 'hadiah_id', 'id');
    }
    public function historyPemenang()
    {
        return $this->hasMany(HistoryPemenangSurvey::class, 'hadiah_id', 'id');
    }

}
