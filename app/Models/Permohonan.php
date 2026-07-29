<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permohonan extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'service_id',
        'nama_pihak_pertama',
        'nama_pihak_kedua',
        'keterangan_tambahan',
        'harga_aset',
        'nop',
        'data_tambahan',
        'file_path',
        'status',
        'notes',
        'ocr_result',
        'ocr_status',
    ];

    protected $casts = [
        'ocr_result' => 'array',
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}