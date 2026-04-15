<?php

// namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;

// class MasterPlotPemenang extends Model
// {
//     use HasFactory;

//     protected $table = 'history_pemenang_survey';
//     protected $primaryKey = 'id';

//     protected $fillable = [
//         'periode_survey_id',
//         'mater_outlet_survey_id',
//         'master_kabupaten_id',
//         'hadiah_id',
//         'status_history',
//     ];
//     public function periodeSurvey()
//     {
//         return $this->belongsTo(PeriodeSurvey::class, 'periode_survey_id');
//     }
//     public function masterOutletSurvey()
//     {
//         return $this->belongsTo(MasterOutletSurvey::class, 'mater_outlet_survey_id');
//     }
//     public function kabupaten()
//     {
//         return $this->belongsTo(MasterKabupaten::class, 'master_kabupaten_id');
//     }
//     public function hadiah()
//     {
//         return $this->belongsTo(MasterHadiah::class, 'hadiah_id');
//     }
// }


// namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;

// class MasterPlotPemenang extends Model
// {
//     use HasFactory;

//     protected $table = 'plot_hadiah_survey';
//     protected $primaryKey = 'id';

//     protected $fillable = [
//         'periode_survey_id',
//         'provinsi_id',
//         'master_kabupaten_id',
//         'master_outlet_survey_id',
//         'hadiah_id',
//         'respondent_id',
//         'is_winning',
//         'status_respondent_assigned',
//         'master_area_id',
//     ];

//     /**
//      * 🔗 Relasi ke tabel master_periode_survey
//      */
//     public function periode()
//     {
//         return $this->belongsTo(MasterPeriode::class, 'periode_survey_id');
//     }

//     /**
//      * 🔗 Relasi ke tabel master_provinsi
//      */
//     public function provinsi()
//     {
//         return $this->belongsTo(MasterProvinsi::class, 'provinsi_id');
//     }

//     /**
//      * 🔗 Relasi ke tabel master_kabupaten
//      */
//     public function kabupaten()
//     {
//         return $this->belongsTo(MasterKabupaten::class, 'master_kabupaten_id');
//     }

//     /**
//      * 🔗 Relasi ke tabel master_outlet_survey
//      */
//     public function outletSurvey()
//     {
//         return $this->belongsTo(MasterOutletSurvey::class, 'master_outlet_survey_id');
//     }

//     /**
//      * 🔗 Relasi ke tabel master_hadiah_survey
//      */
//     public function hadiah()
//     {
//         return $this->belongsTo(MasterHadiah::class, 'hadiah_id');
//     }

//     /**
//      * 🔗 Relasi ke tabel master_respondent
//      */
//     public function respondent()
//     {
//         return $this->belongsTo(MasterRespondent::class, 'respondent_id');
//     }

//     public function area()
//     {
//         return $this->belongsTo(MasterAreaSurvey::class, 'master_area_id', 'id');
//     }
// }

