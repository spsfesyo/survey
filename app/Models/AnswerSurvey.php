<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnswerSurvey extends Model
{
    use HasFactory;
    protected $table = 'answer_survey'; // nama tabel
    protected $primaryKey = 'id'; // nama primary key
    protected $fillable = [
       'master_respondent_id',
       'master_pertanyaan_id',
       'pertanyaan_options_id',
       'jawaban_teks',
       'lainnya'
    ]; // kolom yang dapat diisi


    public function respondent()
    {
        return $this->belongsTo(MasterRespondent::class, 'master_respondent_id');
    }

    public function pertanyaan()
    {
        return $this->belongsTo(MasterPertanyaan::class, 'master_pertanyaan_id');
    }

    public function options()
    {
        return $this->belongsTo(PertanyaanOption::class, 'pertanyaan_options_id');
    }
}
