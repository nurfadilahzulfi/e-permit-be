<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EwpCek extends Model
{
    protected $table    = 'ewp_cek'; // Asumsi nama tabel
    protected $fillable = ['permit_ewp_id', 'model', 'ls_id', 'value'];

    public function permitEwp()
    {
        return $this->belongsTo(PermitEwp::class, 'permit_ewp_id');
    }

    public function ls()
    {
        return $this->morphTo(__FUNCTION__, 'model', 'ls_id');
    }
}
