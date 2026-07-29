<?php

namespace Tests\Feature;

use Tests\TestCase;
use Mockery\MockInterface;
use App\Services\TfIdfService;
use App\Services\GeminiGenerationService;
// use Illuminate\Foundation\Testing\RefreshDatabase;

class IvaControllerTest extends TestCase
{
    // =====================================================================
    // TEST CASE 1: Pengujian Halaman (View)
    // =====================================================================
    public function test_ia_bisa_menampilkan_halaman_index_iva()
    {
        $response = $this->get(route('iva.index'));
        
        $response->assertStatus(200);
        $response->assertViewIs('iva.index');
    }

    // =====================================================================
    // TEST CASE 2: Pengujian Validasi (Negative Case)
    // =====================================================================
    public function test_ia_menolak_pesan_kosong()
    {
        $response = $this->postJson(route('iva.sendMessage'), []);
        
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['message']);
    }

    // =====================================================================
    // TEST CASE 3: Pengujian Database Match (Skor >= 0.85)
    // =====================================================================
    public function test_ia_merespon_dari_database_jika_skor_tfidf_tinggi()
    {
        // 1. MOCKING: Kita palsukan TfIdfService seolah-olah menemukan jawaban pasti
        $this->mock(TfIdfService::class, function (MockInterface $mock) {
            $mock->shouldReceive('findBestMatch')
                 ->once() // Fungsi ini harus dipanggil tepat 1 kali
                 ->with('Syarat AJB apa saja?')
                 ->andReturn([
                     'score' => 0.90, // Skor di atas 0.85
                     'intent' => (object) ['konteks_jawaban' => 'Syarat AJB adalah KTP dan KK.']
                 ]);
        });

        // 2. EKSEKUSI
        $response = $this->postJson(route('iva.sendMessage'), [
            'message' => 'Syarat AJB apa saja?'
        ]);

        // 3. ASSERTION
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'source' => 'database',
            'reply' => 'Syarat AJB adalah KTP dan KK.'
        ]);
    }

    // =====================================================================
    // TEST CASE 4: Pengujian Fallback ke AI (Skor < 0.85 / Tidak Ditemukan)
    // =====================================================================
    public function test_ia_menggunakan_ai_gemini_jika_jawaban_tidak_ada_di_database()
    {
        // 1. MOCKING: TfIdfService tidak menemukan jawaban (return null)
        $this->mock(TfIdfService::class, function (MockInterface $mock) {
            $mock->shouldReceive('findBestMatch')
                 ->once()
                 ->andReturn(null);
        });

        // 2. MOCKING: GeminiService memalsukan balasan (agar tidak menyedot kuota API asli)
        $this->mock(GeminiGenerationService::class, function (MockInterface $mock) {
            $mock->shouldReceive('generateResponse')
                 ->once()
                 ->andReturn('Ini adalah jawaban simulasi dari AI Gemini.');
        });

        $response = $this->postJson(route('iva.sendMessage'), [
            'message' => 'Pertanyaan rumit di luar database'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'source' => 'ai_generative',
            'reply' => 'Ini adalah jawaban simulasi dari AI Gemini.'
        ]);
    }

    // =====================================================================
    // TEST CASE 5: Pengujian Error Handling (Limit API Habis)
    // =====================================================================
    public function test_ia_mengembalikan_status_fallback_jika_api_ai_error()
    {
        // 1. MOCKING: TfIdfService tidak menemukan jawaban
        $this->mock(TfIdfService::class, function (MockInterface $mock) {
            $mock->shouldReceive('findBestMatch')->once()->andReturn(null);
        });

        // 2. MOCKING: GeminiService dipaksa melempar Error/Exception (simulasi API mati)
        $this->mock(GeminiGenerationService::class, function (MockInterface $mock) {
            $mock->shouldReceive('generateResponse')
                 ->once()
                 ->andThrow(new \Exception('Simulasi Error: API Limit Exceeded atau 429 Too Many Requests'));
        });

        $response = $this->postJson(route('iva.sendMessage'), [
            'message' => 'Tolong jelaskan alur pembuatan akta'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'fallback',
            'reply' => 'Maaf, sistem sedang sibuk (Limit Request). Silakan coba 1 menit lagi atau hubungi staf admin kami.'
        ]);
    }
}