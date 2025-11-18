<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EwpCekLs extends Model
{
    protected $table    = 'ewp_cek_ls'; // Asumsi nama tabel
    protected $fillable = ['nama'];
    public $timestamps  = false;

    public function ewpCek()
    {
        return $this->morphMany(EwpCek::class, 'ls', 'model', 'ls_id');
    }
}
