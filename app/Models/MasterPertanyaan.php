<?php

namespace App\Models;

use App\Models\AnswerSurvey;
use App\Models\MasterSection;
use App\Models\TipePertanyaan;
use App\Models\PertanyaanOption;
use App\Models\MasterJenisPertanyaan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MasterPertanyaan extends Model
{
    use HasFactory;

    protected $table = 'master_pertanyaan'; // nama tabel
    protected $primaryKey = 'id'; // nama primary key
    protected $fillable = [
       'pertanyaan', 'master_jenis_pertanyaan_id',
        'master_section_id', 'master_tipe_pertanyaan_id', 'order'
    ]; // kolom yang dapat diisi


    public function jenisPertanyaan()
    {
        return $this->belongsTo(MasterJenisPertanyaan::class, 'master_jenis_pertanyaan_id');
    }

    public function section()
    {
        return $this->belongsTo(MasterSection::class, 'master_section_id');
    }

    public function tipePertanyaan()
    {
        return $this->belongsTo(TipePertanyaan::class, 'master_tipe_pertanyaan_id', 'id');
    }

    public function options()
    {
        return $this->hasMany(PertanyaanOption::class, 'master_pertanyaan_id');
    }

    public function answers()
    {
        return $this->hasMany(AnswerSurvey::class, 'master_pertanyaan_id');
    }

}
