<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GwpCekHseLs extends Model
{
    protected $table    = 'gwp_cek_hse_ls';
    protected $fillable = ['nama'];

    public function gwpCek()
    {
        return $this->morphMany(GwpCek::class, 'ls', 'model', 'ls_id');
    }
}
