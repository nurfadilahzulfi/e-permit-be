<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GwpCekPemohonLs extends Model
{
    protected $table    = 'gwp_cek_pemohon_ls';
    protected $fillable = ['nama'];

    public function gwpCek()
    {
        return $this->morphMany(GwpCek::class, 'ls', 'model', 'ls_id');
    }
}
