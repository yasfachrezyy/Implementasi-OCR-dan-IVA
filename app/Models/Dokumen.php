<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dokumen extends Model
{
    public function permohonan()
    {
        return $this->belongsTo(Permohonan::class);
    }
}
