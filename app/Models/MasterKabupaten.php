<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterKabupaten extends Model
{
    use HasFactory;
    protected $table = 'master_kabupaten'; // nama tabel
    protected $primaryKey = 'id'; // nama primary key
    protected $fillable = [
        'nama_kabupaten', // kolom yang dapat diisi
        'provinsi_id', // kolom yang dapat diisi
    ];



    public function respondents()
    {
        return $this->hasMany(MasterRespondent::class, 'master_kabupaten_id', 'id');
    }

    public function provinsi()
    {
        return $this->belongsTo(MasterProvinsi::class, 'provinsi_id', 'id');
    }

    public function outletSurvey()
    {
        return $this->hasMany(MasterOutletSurvey::class, 'master_kabupaten_id', 'id');
    }
    public function plotHadiah()
    {
        return $this->hasMany(PlotHadiahSurvey::class, 'master_kabupaten_id', 'id');
    }
    public function aturanHadiah()
    {
        return $this->hasMany(AturanHadiahSurvey::class, 'master_kabupaten_id', 'id');
    }
    public function historyPemenang()
    {
        return $this->hasMany(HistoryPemenangSurvey::class, 'master_kabupaten_id', 'id');
    }

}
