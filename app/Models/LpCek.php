<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LpCek extends Model
{
    protected $table    = 'lp_cek'; // Asumsi nama tabel
    protected $fillable = ['permit_lp_id', 'model', 'ls_id', 'value'];

    public function permitLp()
    {
        return $this->belongsTo(PermitLp::class, 'permit_lp_id');
    }

    public function ls()
    {
        return $this->morphTo(__FUNCTION__, 'model', 'ls_id');
    }
}
