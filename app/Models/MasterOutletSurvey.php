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
        'status_blast_wa', // Status blast WA, default 'false'
        'master_area_id', // Foreign key to master_area
        'periode_id', // Foreign key to MasterPeriode
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
    // public function plotPemenang()
    // {
    //     return $this->hasMany(PlotPemenangSurvey::class, 'master_outlet_survey_id', 'id'); // ikut nama kolom di migration (typo)
    // }
    public function area()
    {
        return $this->belongsTo(MasterAreaSurvey::class, 'master_area_id', 'id');
    }
    public function periode()
    {
        return $this->belongsTo(MasterPeriode::class, 'periode_id', 'id');
    }

}
    // =======================
    // 🔹 Relasi Berantai
    // =======================

    // Relasi ke kabupaten melalui area (jika master_area_id → master_kabupaten_id)
    // public function kabupatenViaArea()
    // {
    //     return $this->hasOneThrough(
    //         MasterKabupaten::class,
    //         MasterAreaSurvey::class,
    //         'id',                   // Foreign key di tabel master_area_survey
    //         'id',                   // Foreign key di tabel master_kabupaten
    //         'master_area_id',       // Foreign key di tabel outlet
    //         'master_kabupaten_id'   // Foreign key di tabel master_area_survey
    //     );
    // }

    // Relasi ke provinsi melalui kabupaten (lewat area juga bisa)
    // public function provinsi()
    // {
    //     return $this->hasOneThrough(
    //         MasterProvinsi::class,
    //         MasterKabupaten::class,
    //         'id',                   // Foreign key di kabupaten
    //         'id',                   // Foreign key di provinsi
    //         'master_area_id',       // Foreign key di outlet (jika ada)
    //         'provinsi_id'           // Foreign key di kabupaten
    //     );
    // }

