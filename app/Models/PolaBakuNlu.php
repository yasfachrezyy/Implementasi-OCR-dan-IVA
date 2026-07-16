<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PolaBakuNlu extends Model
{
    use HasFactory;

    // Mendefinisikan nama tabel secara eksplisit agar Laravel tidak mencari tabel 'pola_baku_nlus'
    protected $table = 'pola_baku_nlu';

    // Mengizinkan mass assignment untuk kolom-kolom ini (sangat penting untuk proses Seeding)
    protected $fillable = [
        'intent_id',
        'pola_pertanyaan',
    ];

    /**
     * Relasi Inverse One-to-Many (Belongs-To) ke model Intent.
     * Menandakan bahwa setiap pola pertanyaan terikat pada satu intent spesifik.
     */
    public function intent(): BelongsTo
    {
        return $this->belongsTo(Intent::class, 'intent_id');
    }
}