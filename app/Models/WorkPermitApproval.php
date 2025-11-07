<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkPermitApproval extends Model
{
    /**
     * [DIUBAH] Menunjuk ke nama tabel baru
     */
    protected $table = 'work_permit_approvals';

    /**
     * [DIUBAH] permit_gwp_id -> work_permit_id
     */
    protected $fillable = [
        'work_permit_id',
        'approver_id',
        'role_persetujuan',
        'status_persetujuan',
        'tgl_persetujuan',
        'catatan',
        'urutan', // <-- Pastikan ini ada (dari migrasi sebelumnya)
    ];

    /**
     * [DIUBAH] Relasi ke Izin Kerja "Induk"
     */
    public function workPermit()
    {
        return $this->belongsTo(WorkPermit::class, 'work_permit_id');
    }

    /**
     * [TETAP] Relasi ke User approver
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
}
