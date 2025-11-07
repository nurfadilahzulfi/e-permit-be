<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkPermit extends Model
{
    use HasFactory;

    protected $table = 'work_permits';

    protected $fillable = [
        'nomor_pekerjaan',
        'deskripsi_pekerjaan',
        'langkah_pekerjaan',
        'potensi_bahaya',
        'pengendalian_risiko',
        'lokasi',
        'shift_kerja',
        'pemohon_id',
        'supervisor_id',
        'hse_id',
        'status',
        'tgl_pekerjaan_dimulai',
        'tgl_pekerjaan_selesai',
    ];

    /**
     * Atribut yang harus di-cast ke tipe data asli.
     */
    protected $casts = [
        'tgl_pekerjaan_dimulai' => 'datetime',
        'tgl_pekerjaan_selesai' => 'datetime',
    ];

    // --- RELASI-RELASI BARU ---

    /**
     * Relasi ke User (Pemohon).
     */
    public function pemohon()
    {
        return $this->belongsTo(User::class, 'pemohon_id');
    }

    /**
     * Relasi ke User (Supervisor).
     */
    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    /**
     * Relasi ke User (HSE yang menginisiasi).
     */
    public function hse()
    {
        return $this->belongsTo(User::class, 'hse_id');
    }

    /**
     * Relasi ke log persetujuan (yang tabelnya kita rename).
     */
    public function approvals()
    {
        return $this->hasMany(WorkPermitApproval::class, 'work_permit_id');
    }

    // --- RELASI KE SUB-PERMIT ---

    /**
     * Relasi ke Izin GWP (Anak).
     */
    public function permitGwp()
    {
        return $this->hasOne(PermitGwp::class, 'work_permit_id');
    }

    /**
     * [BARU] Relasi ke log penutupan (completion).
     */
    public function completions()
    {
        return $this->hasMany(WorkPermitCompletion::class, 'work_permit_id');
    }

    public function permitCse()
    {
        return $this->hasOne(PermitCse::class, 'work_permit_id');
    }

    /**
     * (NANTI) Relasi ke Izin CSE (Anak).
     * public function permitCse()
     * {
     * return $this->hasOne(PermitCse::class, 'work_permit_id');
     * }
     */
}
