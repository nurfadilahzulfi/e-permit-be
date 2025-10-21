<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermitGwpCompletion extends Model
{
    protected $table = 'permit_gwp_completion';

    protected $fillable = [
        'permit_gwp_id',
        'completion_id',
        'role_completion',
        'status_completion',
        'tgl_completion',
        'catatan',
    ];

    public function permitGwp()
    {
        return $this->belongsTo(PermitGwp::class, 'permit_gwp_id');
    }
}
