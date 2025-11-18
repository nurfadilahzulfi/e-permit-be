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

    protected $casts = [
        'tgl_pekerjaan_dimulai' => 'datetime',
        'tgl_pekerjaan_selesai' => 'datetime',
    ];

    public static function getStatusText($status)
    {
        switch ($status) {
            case 1:return 'Pending Checklist';
            case 2:return 'Pending Approval';
            case 3:return 'Disetujui';
            case 4:return 'Ditolak';
            case 5:return 'Pending Penutupan Pemohon';
            case 6:return 'Pending Penutupan HSE';
            case 7:return 'Pending Penutupan Supervisor';
            case 8:return 'Ditutup (Arsip)';
            case 10:return 'Pending HSE Review';
            default: return 'Unknown';
        }
    }

    // --- RELASI KE USER ---

    public function pemohon()
    {
        return $this->belongsTo(User::class, 'pemohon_id');
    }
    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }
    public function hse()
    {
        return $this->belongsTo(User::class, 'hse_id');
    }

    // --- RELASI KE LOG ---

    public function approvals()
    {
        return $this->hasMany(WorkPermitApproval::class, 'work_permit_id');
    }
    public function completions()
    {
        return $this->hasMany(WorkPermitCompletion::class, 'work_permit_id');
    }

    // =========================================================
    // [BARU] RELASI KE SEMUA "ANAK" PERMIT
    // =========================================================

    /**
     * Relasi ke GWP (bisa jadi ada, bisa jadi tidak)
     * Kita gunakan hasMany untuk GWP, CSE, HWP, EWP, LP
     * karena 1 Izin Kerja bisa butuh > 1 jenis permit.
     * (Contoh: Galian [EWP] yang juga Panas [HWP])
     */
    public function permitGwp()
    {
        return $this->hasMany(PermitGwp::class, 'work_permit_id');
    }

    /**
     * Relasi ke CSE
     */
    public function permitCse()
    {
        return $this->hasMany(PermitCse::class, 'work_permit_id');
    }

    /**
     * Relasi ke HWP
     */
    public function permitHwp()
    {
        return $this->hasMany(PermitHwp::class, 'work_permit_id');
    }

    /**
     * Relasi ke EWP
     */
    public function permitEwp()
    {
        return $this->hasMany(PermitEwp::class, 'work_permit_id');
    }

    /**
     * Relasi ke LP
     */
    public function permitLp()
    {
        return $this->hasMany(PermitLp::class, 'work_permit_id');
    }
}
