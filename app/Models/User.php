<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Relasi ke permit gwp sebagai pemohon
    public function permitGwp()
    {
        return $this->hasMany(PermitGwp::class, 'pemohon_id');
    }

    // Relasi ke approval permit
    public function approvals()
    {
        return $this->hasMany(PermitGwpApproval::class, 'approver_id');
    }
}
