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

    public function provinsi()
    {
        return $this->belongsTo(MasterProvinsi::class, 'master_provinsi_id');
    }
    public function kabupaten()
    {
        return $this->hasMany(MasterKabupaten::class, 'master_area_id');
    }
}
