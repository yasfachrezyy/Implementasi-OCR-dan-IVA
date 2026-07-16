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
        'file_path',
        'status',
        'notes',
    ];

    // Relasi ke model User (Klien)
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    // Relasi ke model Service (Layanan)
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}