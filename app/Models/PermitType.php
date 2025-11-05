<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermitType extends Model
{
    protected $table = 'permit_types';

    // 👇👇 TAMBAHKAN BARIS INI UNTUK MEMPERBAIKI ERROR 'updated_at' 👇👇
    public $timestamps = false;

    /**
     * deskripsi tidak ada di $fillable kamu sebelumnya, saya tambahkan
     */
    protected $fillable = [
        'nama',
        'kode',
        'deskripsi', // Pastikan ini ada jika kamu ingin mengisinya
    ];

    public function permits()
    {
        return $this->hasMany(PermitGwp::class, 'permit_type_id');
    }
}
