<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermitCse extends Model
{
    protected $table = 'permit_cse';

    protected $fillable = [
        'work_permit_id',
        'permit_type_id',
        'permit_type_kode',
        'gas_tester_name',
        'entry_supervisor_name',
    ];

    /**
     * Relasi kembali ke "Induk" WorkPermit.
     */
    public function workPermit()
    {
        return $this->belongsTo(WorkPermit::class, 'work_permit_id');
    }

    /**
     * Relasi ke lembar jawaban checklist-nya.
     */
    public function checklists()
    {
        return $this->hasMany(CseCek::class, 'permit_cse_id');
    }
}
