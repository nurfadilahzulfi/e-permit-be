<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CseCekGasLs extends Model
{
    protected $table    = 'cse_cek_gas_ls';
    protected $fillable = ['nama'];
    public $timestamps  = false;

    public function cseCek()
    {
        return $this->morphMany(CseCek::class, 'ls', 'model', 'ls_id');
    }
}
