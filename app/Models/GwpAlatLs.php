<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GwpAlatLs extends Model
{
    protected $table    = 'gwp_alat_ls';
    protected $fillable = ['nama'];

    public function gwpCek()
    {
        return $this->morphMany(GwpCek::class, 'ls', 'model', 'ls_id');
    }
}
