<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterKotaSurvey extends Model
{
    use HasFactory;
    protected $table = 'master_kota_survey'; // nama tabel
    protected $primaryKey = 'id'; // nama primary key
    protected $fillable = [
        'kota',

    ]; // kolom yang dapat diisi

    public function respondents()
    {
        return $this->hasMany(MasterRespondent::class, 'kota_id');
    }
}
