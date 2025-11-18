<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkPermitCompletion extends Model
{
    use HasFactory;

    protected $table = 'work_permit_completions';

    protected $fillable = [
        'work_permit_id',
        'user_id',
        'role_penutupan',
        'tgl_penutupan',
        'catatan',
    ];

    /**
     * Relasi kembali ke Izin Kerja Induk.
     */
    public function workPermit()
    {
        return $this->belongsTo(WorkPermit::class, 'work_permit_id');
    }

    /**
     * Relasi ke User yang menandatangani.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
