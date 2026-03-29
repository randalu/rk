<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role()
    {
        return $this->belongsTo(UserRole::class, 'role_id');
    }

    public function salesperson()
    {
        return $this->hasOne(Salesperson::class, 'user_id');
    }

    public function createdBills()
    {
        return $this->hasMany(Bill::class, 'created_by');
    }

    public function actionLogs()
    {
        return $this->hasMany(ActionLog::class, 'user_id');
    }
}