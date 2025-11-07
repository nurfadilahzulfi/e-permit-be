<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermitGwp extends Model
{
    protected $table = 'permit_gwp';

    /**
     * DIUBAH TOTAL: $fillable sekarang jauh lebih ramping.
     * Hanya menyimpan hal spesifik GWP.
     */
    protected $fillable = [
        'work_permit_id', // <-- Kunci ke "Induk"
        'permit_type_id',
        'peralatan_pekerjaan', // <-- Spesifik GWP
        'valid_until',
        'permit_type_kode', // <-- Denormalisasi (biar cepat)
    ];

    // Sembunyikan permit_type_id
    protected $hidden = ['permit_type_id'];

    // --- RELASI-RELASI ---

    /**
     * [BARU] Relasi kembali ke "Induk"
     */
    public function workPermit()
    {
        return $this->belongsTo(WorkPermit::class, 'work_permit_id');
    }

    /**
     * [TETAP] Relasi ke checklist GWP.
     */
    public function checklists()
    {
        return $this->hasMany(GwpCek::class, 'permit_gwp_id');
    }

    /**
     * [TETAP] Relasi ke tipe permit.
     */
    public function type()
    {
        return $this->belongsTo(PermitType::class, 'permit_type_id');
    }

    /**
     * RELASI LAMA (seperti pemohon, supervisor, approvals) DIHAPUS
     * karena sekarang dihandle oleh 'workPermit'.
     */
}
