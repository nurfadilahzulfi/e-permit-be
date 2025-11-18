<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermitLp extends Model
{
    use HasFactory;
    protected $table    = 'permit_lp';
    protected $fillable = [
        'work_permit_id', 'permit_type_id', 'permit_type_kode',
        'crane_capacity', 'crane_type', 'load_weight', 'load_dimension',
        'sling_angle', 'sling_quantity', 'shackle_capacity', 'spreader_beam_capacity',
    ];

    public function workPermit()
    {return $this->belongsTo(WorkPermit::class, 'work_permit_id');}
    public function type()
    {return $this->belongsTo(PermitType::class, 'permit_type_id');}
}
