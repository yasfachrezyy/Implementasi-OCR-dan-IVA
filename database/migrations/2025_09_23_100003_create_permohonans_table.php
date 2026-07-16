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
        Schema::create('permohonans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('users')->onDelete('cascade'); 
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
            $table->string('nama_pihak_pertama');
            $table->string('nama_pihak_kedua')->nullable();
            $table->text('keterangan_tambahan')->nullable();
            $table->decimal('harga_aset', 15, 2)->nullable();
            $table->string('nop')->nullable();
            $table->string('file_path');
            $table->string('status')->default('DiajuKan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permohonans');
    }
};
