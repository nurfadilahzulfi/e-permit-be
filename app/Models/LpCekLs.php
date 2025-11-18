<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LpCekLs extends Model
{
    protected $table    = 'lp_cek_ls'; // Asumsi nama tabel
    protected $fillable = ['nama'];
    public $timestamps  = false;

    public function lpCek()
    {
        return $this->morphMany(LpCek::class, 'ls', 'model', 'ls_id');
    }
}
