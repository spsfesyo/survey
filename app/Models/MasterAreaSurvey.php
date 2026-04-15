<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterAreaSurvey extends Model
{
    use HasFactory;
    protected $table = 'master_area';
    protected $primaryKey = 'id';
    protected $fillable = [
        'nama_area',
        'master_provinsi_id'
    ];

    // public function provinsi()
    // {
    //     return $this->hasOneThrough(
    //         MasterProvinsi::class,
    //         MasterKabupaten::class,
    //         'id',          // foreign key di tabel kabupaten
    //         'id',          // foreign key di tabel provinsi
    //         'master_kabupaten_id', // FK di tabel area_survey
    //         'provinsi_id'  // FK di tabel kabupaten
    //     );
    // }

    public function provinsi()
    {
        return $this->belongsTo(MasterProvinsi::class, 'master_provinsi_id', 'id');
    }

    // public function kabupaten()
    // {
    //     return $this->hasMany(MasterKabupaten::class, 'master_kabupaten_id', 'id');
    // }

    public function kabupaten()
    {
        return $this->belongsTo(MasterKabupaten::class, 'master_kabupaten_id', 'id');
    }

    // public function kabupaten()
    // {
    //     return $this->hasManyThrough(
    //         MasterKabupaten::class,
    //         MasterProvinsi::class,
    //         'id',          // Foreign key di provinsi (local key di area)
    //         'provinsi_id', // Foreign key di kabupaten
    //         'master_provinsi_id', // Foreign key di area
    //         'id'           // Local key di provinsi
    //     );
    // }

    public function respondent()
    {
        return $this->hasMany(MasterOutletSurvey::class, 'master_area_id', 'id');
    }

    public function outletSurvey()
    {
        return $this->hasMany(MasterOutletSurvey::class, 'master_area_id', 'id');
    }

    public function MasterRespondent()
    {
        return $this->hasMany(MasterRespondent::class, 'area_id', 'id');
    }
}
