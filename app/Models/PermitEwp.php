<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermitEwp extends Model
{
    use HasFactory;
    protected $table    = 'permit_ewp';
    protected $fillable = [
        'work_permit_id', 'permit_type_id', 'permit_type_kode',
        'kedalaman_galian_meter', 'tipe_tanah', 'utility_bawah_tanah_teridentifikasi',
        'catatan_utility', 'dokumen_pendukung_jsa', 'dokumen_pendukung_gambar_teknik',
    ];

    public function workPermit()
    {return $this->belongsTo(WorkPermit::class, 'work_permit_id');}
    public function type()
    {return $this->belongsTo(PermitType::class, 'permit_type_id');}
}
