<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('permohonans', function (Blueprint $table) {
            $table->json('ocr_result')->nullable()->after('file_path');
            $table->string('ocr_status')->default('belum_diproses')->after('ocr_result');
        });
    }

    public function down(): void
    {
        Schema::table('permohonans', function (Blueprint $table) {
            $table->dropColumn(['ocr_result', 'ocr_status']);
        });
    }
};
