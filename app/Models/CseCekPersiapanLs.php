<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CseCekPersiapanLs extends Model
{
    protected $table    = 'cse_cek_persiapan_ls';
    protected $fillable = ['nama'];
    public $timestamps  = false; // Asumsi master data tidak perlu timestamp

    public function cseCek()
    {
        return $this->morphMany(CseCek::class, 'ls', 'model', 'ls_id');
    }
}
