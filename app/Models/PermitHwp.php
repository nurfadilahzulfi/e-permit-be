<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermitHwp extends Model
{
    use HasFactory;
    protected $table    = 'permit_hwp';
    protected $fillable = ['work_permit_id', 'permit_type_id', 'permit_type_kode', 'equipment_tools'];

    public function workPermit()
    {return $this->belongsTo(WorkPermit::class, 'work_permit_id');}
    public function type()
    {return $this->belongsTo(PermitType::class, 'permit_type_id');}
}
