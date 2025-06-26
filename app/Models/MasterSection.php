<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterSection extends Model
{
    use HasFactory;

    protected $table = 'master_section'; // nama tabel
    protected $primaryKey = 'id'; // nama primary key
    protected $fillable = [

        'nama_section',

    ]; // kolom yang dapat diisi


    public function pertanyaan()
    {
        return $this->hasMany(MasterPertanyaan::class, 'master_section_id');
    }
}
