<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePermitGwpRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'permit_type_id'      => 'required|exists:permit_types,id',
            'user_id'             => 'required|exists:users,id',
            'pemohon_jenis'       => 'required|in:internal,eksternal',
            'lokasi_pekerjaan'    => 'required|string|max:255',
            'tanggal_pengajuan'   => 'required|date',
            'deskripsi_pekerjaan' => 'required|string',
        ];
    }
}
