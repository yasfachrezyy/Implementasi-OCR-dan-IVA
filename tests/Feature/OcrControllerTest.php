<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Permohonan;
use App\Http\Controllers\OcrController;
use ReflectionClass;
use Illuminate\Support\Facades\Schema; // Tambahkan ini untuk memanipulasi Schema

class OcrControllerTest extends TestCase
{
    // Menggunakan RefreshDatabase agar data uji tidak mengotori database asli
    use RefreshDatabase; 

    // =====================================================================
    // TEST CASE 1: Pengujian Logika Internal (White-Box Testing murni)
    // Menguji fungsi handleDuplicateDocuments yang bersifat private
    // =====================================================================
    /** @test */
    public function test_ia_bisa_merapikan_nama_dokumen_ganda_secara_otomatis()
    {
        $controller = new OcrController();
        
        // Membuka akses fungsi private menggunakan Reflection
        $reflection = new ReflectionClass(OcrController::class);
        $method = $reflection->getMethod('handleDuplicateDocuments');
        $method->setAccessible(true);

        // Data dummy dari Gemini (Ada 2 KTP, 1 KK)
        $inputDummy = [
            'KTP' => ['NIK' => '320301'],
            'KTP 2' => ['NIK' => '320302'],
            'KK' => ['No KK' => '123456']
        ];

        // Eksekusi
        $hasil = $method->invoke($controller, $inputDummy);

        // Pengecekan Hasil (Assertion)
        $this->assertArrayHasKey('KTP (1)', $hasil, 'KTP pertama gagal diubah namanya');
        $this->assertArrayHasKey('KTP (2)', $hasil, 'KTP kedua gagal diubah namanya');
        $this->assertArrayHasKey('KK', $hasil, 'KK tunggal tidak boleh memakai angka');
        $this->assertEquals('320302', $hasil['KTP (2)']['NIK']);
    }

    // =====================================================================
    // TEST CASE 2: Pengujian Validasi (Negative Case)
    // Memastikan sistem menolak jika data verifikasi tidak lengkap
    // =====================================================================
    /** @test */
    public function test_ia_menolak_menyimpan_data_verifikasi_jika_format_kosong()
    {
        // PERUBAHAN: Menggunakan route() sesuai dengan name di web.php
        $response = $this->postJson(route('ocr.save'), []);

        // Pastikan sistem menolak dengan kode 422 Unprocessable Entity (Validasi Gagal)
        $response->assertStatus(422);
        
        // Pastikan pesan error menyebutkan field yang wajib diisi
        $response->assertJsonValidationErrors(['permohonan_id', 'ocr_data']);
    }

    // =====================================================================
    // TEST CASE 3: Pengujian Database & Endpoint (Positive Case)
    // Memastikan data OCR berhasil disimpan ke tabel permohonan
    // =====================================================================
    /** @test */
    public function test_ia_berhasil_menyimpan_data_ocr_dan_mengubah_status()
    {
        // 1. Siapkan data pancingan di database (Membuat permohonan dummy)
        
        // PERUBAHAN: Matikan aturan Foreign Key sementara
        Schema::disableForeignKeyConstraints();

        $permohonan = Permohonan::create([
            'client_id' => 1,
            'service_id' => 1,
            'nama_pihak_pertama' => 'Penguji',
            'status' => 'Diajukan',
            'ocr_status' => 'belum',
            'file_path' => 'dummy.pdf'
        ]);

        // PERUBAHAN: Nyalakan kembali aturan Foreign Key agar database tetap aman
        Schema::enableForeignKeyConstraints();

        $dataOcrDummy = [
            'KTP' => ['NIK' => '1234567890123456']
        ];

        // 2. Eksekusi Request menggunakan route()
        $response = $this->postJson(route('ocr.save'), [
            'permohonan_id' => $permohonan->id,
            'ocr_data'      => $dataOcrDummy,
        ]);

        // 3. Pengecekan Hasil
        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);

        // Pastikan data benar-benar berubah di dalam database
        $this->assertDatabaseHas('permohonans', [
            'id' => $permohonan->id,
            'ocr_status' => 'terverifikasi',
        ]);
    }

    // =====================================================================
    // TEST CASE 4: Pengujian File System (Negative Case)
    // Memastikan sistem memberikan error 404 jika PDF tidak ditemukan
    // =====================================================================
    /** @test */
    public function test_ia_mengembalikan_error_404_jika_file_pdf_tidak_ditemukan_saat_count_pages()
    {
        // PERUBAHAN: Menggunakan route('ocr.count') sesuai dengan name di web.php
        $response = $this->postJson(route('ocr.count'), [
            'file_path' => 'folder/file_gaib_yang_tidak_ada.pdf'
        ]);

        // Pastikan gagal dan mengembalikan JSON error 404
        $response->assertStatus(404);
        $response->assertJsonStructure(['status', 'message']);
        $response->assertJsonPath('status', 'error');
    }
}