<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermitGwp extends Model
{
    protected $table = 'permit_gwp';

    /**
     * DIUBAH: $fillable disesuaikan dengan migrasi baru kita.
     * - 'pemilik_lokasi_jenis' DIHAPUS
     * - 'supervisor_id' DITAMBAHKAN
     * - 'valid_until' DITAMBAHKAN
     */
    protected $fillable = [
        'permit_type_id',
        'nomor',
        'tgl_permohonan',
        'shift_kerja',
        'lokasi',
        'deskripsi_pekerjaan',
        'peralatan_pekerjaan',
        'pemohon_id',
        'pemohon_jenis',
        'supervisor_id', // <-- BARU
        'status',
        'valid_until', // <-- BARU
    ];

    // Sembunyikan permit_type_id dari hasil serialisasi JSON
    protected $hidden = ['permit_type_id'];

    // Relasi ke permit type
    public function type()
    {
        return $this->belongsTo(PermitType::class, 'permit_type_id');
    }

    // Relasi ke user (pemohon)
    public function pemohon()
    {
        return $this->belongsTo(User::class, 'pemohon_id');
    }

    /**
     * RELASI BARU: Mendapatkan data Supervisor (User) yang ditugaskan.
     */
    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    // Relasi ke approval
    public function approvals()
    {
        return $this->hasMany(PermitGwpApproval::class, 'permit_gwp_id');
    }

    // Relasi ke completion
    public function completions()
    {
        return $this->hasMany(PermitGwpCompletion::class, 'permit_gwp_id');
    }
}
