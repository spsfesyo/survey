<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'user_survey'; // nama tabel yang digunakan
    protected $primaryKey = 'id'; // nama primary key
    protected $fillable = [
        'role_id',
        'name',
        'email',
        'username',
        'password',
    ];

    public function role()
    {
        return $this->belongsTo(RoleSurvey::class, 'role_id');
    }
}
