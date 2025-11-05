<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
// <-- 1. DITAMBAHKAN

class User extends Authenticatable
{
    use Notifiable, HasApiTokens, HasFactory;

    /**
     * Tentukan nama tabel yang sebenarnya di database (dari 'users' ke 'user').
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
        'role', // <-- 2. DITAMBAHKAN
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * 3. DITAMBAHKAN: Mutator untuk otomatis HASH password saat disimpan
     * Ini akan memperbaiki masalah "password" di Tinker dan UserController.
     */
    public function setPasswordAttribute($value)
    {
        // Jangan hash jika password sudah di-hash
        if (Hash::needsRehash($value)) {
            $this->attributes['password'] = Hash::make($value);
        } else {
            $this->attributes['password'] = $value;
        }
    }

    // --- RELASI ---
    // Relasi-relasi ini sudah benar dari file kamu

    /**
     * Relasi ke Izin GWP yang DIBUATNYA.
     */
    public function permitsCreated()
    {
        return $this->hasMany(PermitGwp::class, 'pemohon_id');
    }

    /**
     * Relasi ke Izin GWP yang perlu dia SUPERVISI.
     */
    public function permitsToSupervise()
    {
        return $this->hasMany(PermitGwp::class, 'supervisor_id');
    }

    /**
     * Relasi ke LOG APPROVAL yang harus dia kerjakan.
     */
    public function approvalTasks()
    {
        return $this->hasMany(PermitGwpApproval::class, 'approver_id');
    }
}
