<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterProvinsi extends Model
{
    use HasFactory;
    protected $table = 'master_provinsi'; // nama tabel
    protected $primaryKey = 'id'; // nama primary key
    protected $fillable = [
        'nama_provinsi',
    ]; // kolom yang dapat diisi

    public function respondents()
    {
        return $this->hasMany(MasterRespondent::class, 'provinsi_id');
    }
}
