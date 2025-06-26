<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PertanyaanOption extends Model
{
    use HasFactory;
    protected $table = 'pertanyaan_options'; // nama tabel
    protected $primaryKey = 'id'; // nama primary key
    protected $fillable = [
        'master_pertanyaan_id',
        'options',
        'is_other'
    ]; // kolom yang dapat diisi

    public function pertanyaan()
    {
        return $this->belongsTo(MasterPertanyaan::class, 'master_pertanyaan_id');
    }

    public function answers()
    {
        return $this->hasMany(AnswerSurvey::class, 'pertanyaan_options_id');
    }
}
