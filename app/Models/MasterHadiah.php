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
}
