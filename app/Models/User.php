<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     * Semua kolom NOT NULL dari migration harus dimasukkan di sini.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama',
        'nip',        // Wajib ditambahkan
        'divisi',     // Wajib ditambahkan
        'jabatan',    // Wajib ditambahkan
        'perusahaan', // Wajib ditambahkan
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
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
