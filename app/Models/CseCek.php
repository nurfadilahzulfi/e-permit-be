<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CseCek extends Model
{
    protected $table = 'cse_cek';

    protected $fillable = [
        'permit_cse_id',
        'model',
        'ls_id',
        'value',
    ];

    /**
     * Relasi ke "Anak" Permit CSE.
     */
    public function permitCse()
    {
        return $this->belongsTo(PermitCse::class, 'permit_cse_id');
    }

    /**
     * Relasi Morph ke master checklist (CseCekPersiapanLs atau CseCekGasLs).
     */
    public function ls()
    {
        return $this->morphTo(__FUNCTION__, 'model', 'ls_id');
    }
}
