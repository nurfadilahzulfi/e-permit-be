<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HwpCek extends Model
{
    protected $table    = 'hwp_cek';
    protected $fillable = ['permit_hwp_id', 'model', 'ls_id', 'value'];
    public function permitHwp()
    {
        return $this->belongsTo(PermitHwp::class, 'permit_hwp_id');
    }
    public function ls()
    {
        return $this->morphTo(__FUNCTION__, 'model', 'ls_id');
    }
}
