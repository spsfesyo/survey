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
        'nama_outlet',
        'sps_internal_name',
        'telepone_outlet',
        'kode_unik',
        'status_kode_unik'
    ];

    public function respondents()
    {
        return $this->hasMany(MasterRespondent::class, 'master_outlet_survey_id');
    }
}
