<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HwpCekLs extends Model
{
    protected $table    = 'hwp_cek_ls'; // Asumsi nama tabel
    protected $fillable = ['nama'];
    public $timestamps  = false;

    public function hwpCek()
    {
        return $this->morphMany(HwpCek::class, 'ls', 'model', 'ls_id');
    }
}
