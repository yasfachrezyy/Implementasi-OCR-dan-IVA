<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Intent extends Model
{
    use HasFactory;

    protected $table = 'intents';
    
    protected $fillable = [
        'nama_intent',
        'konteks_jawaban',
        'is_active',
    ];

    public function polaBaku(): HasMany
    {
        return $this->hasMany(PolaBakuNlu::class, 'intent_id');
    }
}