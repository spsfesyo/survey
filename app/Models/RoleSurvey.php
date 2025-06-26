<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleSurvey extends Model
{
    use HasFactory;
    protected $table = 'role_survey'; // nama tabel yang digunakan
    protected $primaryKey = 'id'; // nama primary key
    protected $fillable = [
        'role_name', // kolom nama peran yang unik
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'role_id');
    }

}
