<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GwpCek extends Model
{
    protected $table = 'gwp_cek';

    protected $fillable = [
        'permit_gwp_id',
        'model',
        'ls_id',
        'value',
    ];

    public function permitGwp()
    {
        return $this->belongsTo(PermitGwp::class, 'permit_gwp_id');
    }

    // morph ke LS (list item)
    public function ls()
    {
        return $this->morphTo(__FUNCTION__, 'model', 'ls_id');
    }
}
