<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pola_baku_nlu', function (Blueprint $table) {
            $table->id();
            
            // Relasi Foreign Key ke tabel intents
            $table->foreignId('intent_id')
                  ->constrained('intents')
                  ->onDelete('cascade');
                  
            $table->string('pola_pertanyaan'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pola_baku_nlu');
    }
};