<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermitType extends Model
{
    protected $table = 'permit_types';

    protected $fillable = [
        'nama',
        'kode',
        'deskripsi',
    ];

    public function permits()
    {
        return $this->hasMany(PermitGwp::class, 'permit_type_id');
    }
}
