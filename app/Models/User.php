<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens, HasFactory;

    /**
     * Tentukan nama tabel yang sebenarnya di database (dari 'users' ke 'user').
     * Karena Anda mengubah nama tabel secara manual menjadi 'user' (tunggal).
     */
    protected $table = 'user'; // FIX: Nama tabel tunggal

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama',
        'nip',
        'divisi',
        'jabatan',
        'perusahaan',
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

    /**
     * The attributes that should be cast.
     * Tidak menggunakan cast 'hashed' karena ini untuk Laravel 10+.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    // Catatan untuk Laravel 8: Pastikan Anda MENGGUNAKAN Hash::make()
    // di Controller/Mutator Anda saat menyimpan password baru.

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
