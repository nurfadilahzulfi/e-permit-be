<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermitGwpApproval extends Model
{
    protected $table = 'permit_gwp_approval';

    protected $fillable = [
        'permit_gwp_id',
        'approver_id',
        'role_persetujuan',
        'status_persetujuan',
        'tgl_persetujuan',
        'catatan',
    ];

    public function permitGwp()
    {
        return $this->belongsTo(PermitGwp::class, 'permit_gwp_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
}
