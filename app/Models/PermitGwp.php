<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermitGwp extends Model
{
    protected $table = 'permit_gwp';

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
        'pemilik_lokasi_jenis',
        'status',
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

    // Relasi ke checklist (morphMany)
    public function gwpCek()
    {
        return $this->hasMany(GwpCek::class, 'permit_gwp_id');
    }
}
