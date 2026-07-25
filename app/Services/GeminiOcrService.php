<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * GeminiOcrService
 *
 * Menggunakan Google Gemini Vision API untuk mengekstrak data dari
 * gambar halaman dokumen berkas persyaratan AJB (Akta Jual Beli).
 *
 * Lebih akurat dibanding Tesseract+Regex karena Gemini memahami
 * konteks, layout dua kolom, stempel, dan variasi format dokumen daerah.
 */
class GeminiOcrService
{
    protected string $apiKey;
    protected string $apiUrl;

    // Model yang digunakan — gemini-2.5-flash mendukung vision (image input)
    public function __construct()
    {
        // Model dapat diubah via .env (misal: gemini-2.5-flash-lite, gemini-2.5-flash, gemini-1.5-flash)
        $this->model = env('GEMINI_OCR_MODEL', 'gemini-2.5-flash-lite');

        // Gunakan key khusus OCR jika ada, fallback ke key umum
        $this->apiKey = config('services.gemini_ocr.key')
            ?: env('GEMINI_OCR_API_KEY')
            ?: env('GEMINI_API_KEY', '');
        $this->apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}";
    }

    /**
     * Ekstrak data dokumen AJB dari gambar (path file JPEG/PNG).
     *
     * @param  string $imagePath  Path absolut ke file gambar (hasil render Ghostscript)
     * @param  int    $pageNumber Nomor halaman (untuk logging)
     * @return array  Array data terstruktur hasil ekstraksi
     */
    public function extractFromImage(string $imagePath, int $pageNumber = 1): array
    {
        if (empty($this->apiKey)) {
            throw new \Exception('GEMINI_API_KEY belum dikonfigurasi di file .env');
        }

        if (!file_exists($imagePath)) {
            throw new \Exception("File gambar tidak ditemukan: {$imagePath}");
        }

        // Encode gambar ke base64
        $imageData   = base64_encode(file_get_contents($imagePath));
        $mimeType    = $this->detectMimeType($imagePath);

        $prompt = $this->buildPrompt();

        $payload = [
            'contents' => [
                [
                    'parts' => [
                        [
                            'inline_data' => [
                                'mime_type' => $mimeType,
                                'data'      => $imageData,
                            ],
                        ],
                        [
                            'text' => $prompt,
                        ],
                    ],
                ],
            ],
            'generationConfig' => [
                'temperature'     => 0.0,   // Deterministik — tidak kreatif
                'response_mime_type' => 'application/json',
            ],
        ];

        // ------------------------------------------------------------------
        // Kirim ke Gemini dengan retry otomatis jika kena rate limit (429)
        // Free tier: ~10 RPM — jeda antar percobaan: 15s, 30s, 60s
        // ------------------------------------------------------------------
        $maxRetry   = 3;
        $retryDelay = [15, 30, 60]; // detik tunggu per percobaan
        $response   = null;

        for ($attempt = 1; $attempt <= $maxRetry; $attempt++) {
            $response = Http::withoutVerifying()
                ->timeout(90)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($this->apiUrl, $payload);

            if ($response->successful()) break;

            $status = $response->status();

            // 429 = rate limit — tunggu lalu coba lagi
            if ($status === 429 && $attempt < $maxRetry) {
                $wait = $retryDelay[$attempt - 1];
                Log::warning("Gemini OCR [{$pageNumber}]: Rate limit 429, tunggu {$wait}s (percobaan {$attempt}/{$maxRetry})");
                sleep($wait);
                continue;
            }

            // Error lain atau sudah max retry
            $errorBody = $response->body();
            $errorJson = json_decode($errorBody, true);
            $errorMsg  = $errorJson['error']['message'] ?? $errorBody;

            if ($status === 429) {
                // Setelah 3x retry masih 429 → kemungkinan RPD habis
                throw new \Exception(
                    "Rate limit Gemini API terlampaui (429). " .
                    "Kemungkinan kuota harian (RPD) sudah habis. " .
                    "Coba lagi besok atau gunakan API key kedua (GEMINI_OCR_API_KEY)."
                );
            }

            Log::error("Gemini OCR API Error [{$pageNumber}]", ['status' => $status, 'body' => $errorBody]);
            throw new \Exception("Gemini API error {$status}: " . substr($errorMsg, 0, 300));
        }

        $raw = $response->json();

        // Ambil teks dari respons
        $textContent = $raw['candidates'][0]['content']['parts'][0]['text'] ?? '';

        if (empty($textContent)) {
            Log::warning("Gemini OCR [{$pageNumber}]: Respons kosong", ['raw' => $raw]);
            return [];
        }

        // Parse JSON dari respons Gemini
        return $this->parseGeminiResponse($textContent, $pageNumber);
    }

    // =========================================================================
    // PROMPT BUILDER
    // =========================================================================

    /**
     * Bangun prompt detail untuk ekstraksi dokumen AJB.
     * Prompt meminta Gemini output JSON berstruktur dengan semua tipe dokumen.
     */
    private function buildPrompt(): string
    {
        return <<<'PROMPT'
Kamu adalah sistem OCR cerdas untuk kantor Notaris & PPAT Indonesia.
Analisis gambar halaman dokumen ini dan ekstrak semua data yang terlihat.

Dokumen ini adalah bagian dari berkas persyaratan AJB (Akta Jual Beli) yang mungkin berisi:
- KTP (Kartu Tanda Penduduk)
- KK (Kartu Keluarga)
- Akta Kelahiran
- Buku Nikah / Surat Cerai / Surat Keterangan Kematian
- Surat Keterangan Waris
- Sertifikat Tanah (SHM / HGB / dll)
- SPPT PBB (Surat Pemberitahuan Pajak Terhutang Pajak Bumi dan Bangunan)
- STTS / Bukti Lunas PBB
- NPWP — HANYA dari KARTU NPWP FISIK (bukan dari field NPWP di dokumen lain)
- Surat Persetujuan Suami/Istri

INSTRUKSI PENTING:
1. Identifikasi jenis dokumen yang ada di halaman ini (bisa lebih dari satu)
2. Ekstrak HANYA data yang benar-benar tertulis/terlihat jelas di gambar
3. Jika suatu field tidak ada/tidak terbaca, isi dengan string kosong ""
4. Untuk NIK dan No KK: pastikan tepat 16 digit angka, perbaiki karakter OCR umum (O→0, I→1, l→1)
5. Untuk tanggal: gunakan format DD-MM-YYYY atau tulis apa adanya
6. Untuk nama: tulis sesuai di dokumen (HURUF KAPITAL jika memang demikian)
7. Untuk nomor sertifikat tanah: cari label "No." atau "Nomor" di bagian identitas sertifikat, bukan tahun
8. Untuk NOP SPPT: format XX.XX.XXX.XXX.XXX-XXXX.X (biasanya 18 digit dengan titik-strip)
9. KHUSUS NPWP: Ekstrak data NPWP HANYA jika halaman ini berisi KARTU NPWP FISIK.
   Ciri kartu NPWP: ada logo "npwp" atau "DJP" atau tulisan "DIREKTORAT JENDERAL PAJAK",
   nomor besar di tengah kartu (format 16 digit baru: XXXXXXXXXXXXXXXX, atau format lama: XX.XXX.XXX.X-XXX.XXX),
   dan nama pemegang kartu.
   JANGAN ekstrak NPWP dari field "NPWP Wajib Pajak" atau "No. NPWP" yang tercantum
   di dalam dokumen STTS, SPPT PBB, SSB, atau dokumen pajak lainnya —
   itu bukan kartu NPWP, melainkan hanya referensi nomor di dokumen tersebut.
10. KHUSUS SURAT KETERANGAN WARIS: Untuk field "Daftar Ahli Waris", tuliskan SETIAP AHLI WARIS DALAM BARIS BARU (dipisahkan karakter newline \n).
    Cantumkan SELURUH DATA DIRI yang ada di dokumen untuk masing-masing ahli waris:
    - Nomor urut & Nama Lengkap
    - NIK (jika tercantum)
    - Tempat & Tanggal Lahir (jika ada)
    - Alamat / Tempat Tinggal (jika ada)
    - Status/Keterangan Khusus yang SANGAT PENTING (misalnya: "(Telah Meninggal Dunia pada tanggal ...)", "(Anak Kandung)", "(Istri/Suami)").
    CONTOH FORMAT DAFTAR AHLI WARIS:
    1. ACENG KURNIA — NIK: 3203..., TTL: Cianjur, 20 Juni 1960, Alamat: Kp. Simpang
    2. EDI KUSWARA (Telah Meninggal Dunia pada tanggal 10 Mei 2015) — NIK: 3203...
    3. ATIK KURNIASIH — NIK: 3203...
    JANGAN menggabungkan daftar ahli waris dalam 1 baris tanpa newline!

Kembalikan HANYA JSON valid dengan struktur berikut (isi hanya dokumen yang ADA di halaman ini):

{
  "dokumen_terdeteksi": ["KTP", "KK"],
  "KTP": {
    "NIK": "",
    "Nama Lengkap": "",
    "Tempat Lahir": "",
    "Tanggal Lahir": "",
    "Jenis Kelamin": "",
    "Golongan Darah": "",
    "Alamat": "",
    "RT": "",
    "RW": "",
    "Kelurahan/Desa": "",
    "Kecamatan": "",
    "Kabupaten/Kota": "",
    "Provinsi": "",
    "Agama": "",
    "Status Perkawinan": "",
    "Pekerjaan": "",
    "Kewarganegaraan": "",
    "Berlaku Hingga": ""
  },
  "KK": {
    "No KK": "",
    "Kepala Keluarga": "",
    "Alamat": "",
    "RT": "",
    "RW": "",
    "Kelurahan/Desa": "",
    "Kecamatan": "",
    "Kabupaten/Kota": "",
    "Provinsi": "",
    "Anggota": [
      {
        "No": "",
        "Nama": "",
        "NIK": "",
        "Jenis Kelamin": "",
        "Tempat Lahir": "",
        "Tanggal Lahir": "",
        "Agama": "",
        "Pendidikan": "",
        "Pekerjaan": "",
        "Status Perkawinan": "",
        "Hubungan Keluarga": "",
        "Kewarganegaraan": ""
      }
    ]
  },
  "Akta Kelahiran": {
    "Nomor Akta Kelahiran": "",
    "Nama Lengkap Anak": "",
    "Tempat Lahir": "",
    "Tanggal Lahir": "",
    "Nama Ayah": "",
    "Nama Ibu": ""
  },
  "Buku Nikah": {
    "Nomor Akta Nikah": "",
    "Nama Suami": "",
    "NIK Suami": "",
    "Nama Istri": "",
    "NIK Istri": "",
    "Tanggal Nikah": "",
    "Tempat Nikah": ""
  },
  "Surat Cerai": {
    "Nomor Putusan": "",
    "Nama Penggugat": "",
    "Nama Tergugat": "",
    "Tanggal Putusan": ""
  },
  "Surat Kematian": {
    "Nama Almarhum": "",
    "Tanggal Meninggal": "",
    "Tempat Meninggal": "",
    "Penyebab": ""
  },
  "Surat Keterangan Waris": {
    "Nomor Surat Waris": "",
    "Nama Pewaris": "",
    "Tanggal Meninggal Pewaris": "",
    "Daftar Ahli Waris": "",
    "Kesimpulan Waris": "",
    "Disahkan Oleh": "",
    "Tanggal Surat": ""
  },
  "Sertifikat Tanah": {
    "Jenis Hak": "",
    "Nomor Sertifikat": "",
    "Nomor Induk Bidang (NIB)": "",
    "Desa/Kelurahan": "",
    "Kecamatan": "",
    "Kabupaten/Kota": "",
    "Provinsi": "",
    "Luas Tanah (m2)": "",
    "Atas Nama": "",
    "Tanggal Terbit": ""
  },
  "SPPT PBB": {
    "Nomor Objek Pajak (NOP)": "",
    "Tahun Pajak": "",
    "Nama Wajib Pajak": "",
    "Alamat Wajib Pajak": "",
    "Letak Objek Pajak": "",
    "Luas Tanah (m2)": "",
    "Luas Bangunan (m2)": "",
    "Nilai Jual Objek Pajak (NJOP)": "",
    "PBB Terutang": ""
  },
  "Bukti Lunas PBB (STTS)": {
    "Nomor Objek Pajak (NOP)": "",
    "Tahun Pajak": "",
    "Nama Wajib Pajak": "",
    "Jumlah Bayar": "",
    "Tanggal Bayar": "",
    "Bank/Tempat Bayar": "",
    "Nomor Transaksi": ""
  },
  "NPWP": {
    "No NPWP": "",
    "Nama Terdaftar": "",
    "Alamat": "",
    "KPP": ""
  },
  "Surat Persetujuan Suami/Istri": {
    "Nama Pemberi Persetujuan": "",
    "NIK Pemberi Persetujuan": "",
    "Hubungan": "",
    "Nama Penerima Persetujuan": "",
    "Keterangan": "",
    "Tanggal Surat": ""
  }
}

PENTING: 
- Untuk array "dokumen_terdeteksi", isi HANYA nama dokumen yang benar-benar ada di halaman ini
- Jika halaman berisi lebih dari satu dokumen (misal KTP dan KK dalam satu halaman), isi keduanya
- Jika halaman ini adalah cover, pengantar, atau dokumen lain yang tidak ada dalam daftar, kembalikan {"dokumen_terdeteksi": [], "catatan": "deskripsi singkat isi halaman"}
- NPWP hanya dimasukkan ke "dokumen_terdeteksi" jika halaman ini benar-benar memuat KARTU NPWP FISIK (ada logo npwp/DJP). Jika yang ada hanya field NPWP di dalam STTS/SPPT/dokumen pajak lain, JANGAN tambahkan "NPWP" ke dokumen_terdeteksi.
PROMPT;
    }

    // =========================================================================
    // RESPONSE PARSER
    // =========================================================================

    /**
     * Parse respons teks dari Gemini menjadi array terstruktur.
     * Gemini kadang menambahkan markdown code fence ```json ... ``` — kita strip dulu.
     */
    private function parseGeminiResponse(string $text, int $pageNumber): array
    {
        // Strip markdown code fence jika ada
        $text = preg_replace('/^```(?:json)?\s*/m', '', $text);
        $text = preg_replace('/```\s*$/m', '', $text);
        $text = trim($text);

        $data = json_decode($text, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::warning("Gemini OCR [{$pageNumber}]: Gagal parse JSON", [
                'error' => json_last_error_msg(),
                'text'  => substr($text, 0, 500),
            ]);
            // Coba cari JSON di dalam teks jika ada teks sebelum/sesudahnya
            if (preg_match('/\{[\s\S]+\}/m', $text, $m)) {
                $data = json_decode($m[0], true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    return [];
                }
            } else {
                return [];
            }
        }

        return $this->normalizeExtractedData($data, $pageNumber);
    }

    /**
     * Normalisasi dan filter data hasil ekstraksi Gemini.
     * Hanya kembalikan dokumen yang ada di "dokumen_terdeteksi".
     */
    private function normalizeExtractedData(array $data, int $pageNumber): array
    {
        $detected = $data['dokumen_terdeteksi'] ?? [];

        if (empty($detected)) {
            Log::info("Gemini OCR [{$pageNumber}]: Tidak ada dokumen terdeteksi", [
                'catatan' => $data['catatan'] ?? '-',
            ]);
            return [];
        }

        // Mapping nama kunci Gemini → nama tampilan di frontend
        $keyMapping = [
            'KTP'                          => 'KTP',
            'KK'                           => 'Kartu Keluarga',
            'Akta Kelahiran'               => 'Akta Kelahiran',
            'Buku Nikah'                   => 'Buku Nikah',
            'Surat Cerai'                  => 'Surat Cerai',
            'Surat Kematian'               => 'Surat Keterangan Kematian',
            'Surat Keterangan Waris'       => 'Surat Keterangan Waris',
            'Sertifikat Tanah'             => 'Sertifikat Tanah',
            'SPPT PBB'                     => 'SPPT PBB',
            'Bukti Lunas PBB (STTS)'       => 'Bukti Lunas PBB (STTS)',
            'NPWP'                         => 'NPWP',
            'Surat Persetujuan Suami/Istri'=> 'Surat Persetujuan Suami/Istri',
        ];

        $result = [];

        foreach ($detected as $docType) {
            if (!isset($data[$docType])) continue;

            $docData    = $data[$docType];
            $displayKey = $keyMapping[$docType] ?? $docType;

            // Khusus KK: flatten array anggota menjadi field Anggota 1, Anggota 2, dst
            if ($docType === 'KK' && isset($docData['Anggota']) && is_array($docData['Anggota'])) {
                $anggota = $docData['Anggota'];
                unset($docData['Anggota']);

                foreach ($anggota as $idx => $member) {
                    $num  = $idx + 1;
                    $nama = $member['Nama'] ?? '';
                    $nik  = $member['NIK']  ?? '';
                    $hub  = $member['Hubungan Keluarga'] ?? '';

                    if (!empty($nama) || !empty($nik)) {
                        $label = "Anggota {$num}";
                        if (!empty($hub)) $label .= " ({$hub})";
                        $docData[$label] = trim("{$nama}" . (!empty($nik) ? " — {$nik}" : ''));
                    }
                }
            }

            // Filter field yang benar-benar ada nilainya
            $filtered = array_filter($docData, function ($val) {
                if (is_array($val)) return !empty($val);
                return !is_null($val) && trim((string) $val) !== '';
            });

            if (!empty($filtered)) {
                $result[$displayKey] = $filtered;
            }
        }

        return $result;
    }

    // =========================================================================
    // HELPER
    // =========================================================================

    private function detectMimeType(string $path): string
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        return match ($ext) {
            'png'  => 'image/png',
            'webp' => 'image/webp',
            default => 'image/jpeg',
        };
    }

    // =========================================================================
    // MULTI-IMAGE: Kirim semua halaman sekaligus dalam 1 request
    // =========================================================================

    /**
     * Ekstrak data dari SEMUA halaman dokumen sekaligus dalam 1 request API.
     * Jauh lebih hemat RPD dibanding per-halaman (14 halaman = 1 request, bukan 14).
     *
     * @param  string[] $imagePaths  Array path absolut gambar tiap halaman (urut dari hal. 1)
     * @param  int      $totalPages  Total halaman dokumen
     * @return array    Array data terstruktur seluruh dokumen yang ditemukan
     */
    public function extractFromMultipleImages(array $imagePaths, int $totalPages = 0): array
    {
        if (empty($this->apiKey)) {
            throw new \Exception('GEMINI_API_KEY belum dikonfigurasi di file .env');
        }

        if (empty($imagePaths)) {
            throw new \Exception('Tidak ada gambar yang dikirim ke Gemini.');
        }

        $total = $totalPages ?: count($imagePaths);
        $parts = [];

        // Susun semua gambar dengan label halaman di antara masing-masing
        foreach ($imagePaths as $i => $imagePath) {
            if (!file_exists($imagePath)) {
                Log::warning("Multi-OCR: Gambar halaman " . ($i + 1) . " tidak ditemukan, dilewati.");
                continue;
            }

            $parts[] = ['text' => "=== HALAMAN " . ($i + 1) . " DARI {$total} ==="];
            $parts[] = [
                'inline_data' => [
                    'mime_type' => $this->detectMimeType($imagePath),
                    'data'      => base64_encode(file_get_contents($imagePath)),
                ],
            ];
        }

        // Prompt di akhir, setelah semua gambar
        $parts[] = ['text' => $this->buildPromptMultiPage($total)];

        $payload = [
            'contents' => [
                ['parts' => $parts],
            ],
            'generationConfig' => [
                'temperature'        => 0.0,
                'response_mime_type' => 'application/json',
            ],
        ];

        // Retry dengan exponential backoff untuk 429
        $maxRetry   = 3;
        $retryDelay = [15, 30, 60];
        $response   = null;

        for ($attempt = 1; $attempt <= $maxRetry; $attempt++) {
            $response = Http::withoutVerifying()
                ->timeout(180) // multi-image butuh waktu lebih lama
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($this->apiUrl, $payload);

            if ($response->successful()) break;

            $status = $response->status();

            if ($status === 429 && $attempt < $maxRetry) {
                $wait = $retryDelay[$attempt - 1];
                Log::warning("Gemini Multi-OCR: Rate limit 429, tunggu {$wait}s (percobaan {$attempt}/{$maxRetry})");
                sleep($wait);
                continue;
            }

            $errorBody = $response->body();
            $errorJson = json_decode($errorBody, true);
            $errorMsg  = $errorJson['error']['message'] ?? $errorBody;

            if ($status === 429) {
                throw new \Exception(
                    "Rate limit Gemini API (429). Kuota harian (RPD) habis. " .
                    "Coba lagi besok atau isi GEMINI_OCR_API_KEY di .env dengan key dari akun Google lain."
                );
            }

            Log::error("Gemini Multi-OCR API Error", ['status' => $status, 'body' => substr($errorBody, 0, 500)]);
            throw new \Exception("Gemini API error {$status}: " . substr($errorMsg, 0, 300));
        }

        $raw         = $response->json();
        $textContent = $raw['candidates'][0]['content']['parts'][0]['text'] ?? '';

        if (empty($textContent)) {
            Log::warning("Gemini Multi-OCR: Respons kosong", ['raw' => $raw]);
            return [];
        }

        return $this->parseGeminiResponse($textContent, 0);
    }

    /**
     * Prompt untuk mode multi-halaman — Gemini menerima semua halaman sekaligus.
     */
    private function buildPromptMultiPage(int $totalPages): string
    {
        return <<<PROMPT
Kamu adalah sistem OCR cerdas untuk kantor Notaris & PPAT Indonesia.
Di atas ini kamu telah menerima {$totalPages} gambar halaman dari SATU berkas persyaratan AJB (Akta Jual Beli).
Setiap gambar diberi label "=== HALAMAN X DARI {$totalPages} ===" untuk memudahkan identifikasi.

Tugas kamu: Analisis SELURUH halaman dan ekstrak semua data dari SEMUA dokumen yang ada di dalam berkas ini.

Dokumen yang mungkin ada di dalam berkas ini:
- KTP (Kartu Tanda Penduduk)
- KK (Kartu Keluarga)
- Akta Kelahiran
- Buku Nikah / Surat Cerai / Surat Keterangan Kematian
- Surat Keterangan Waris
- Sertifikat Tanah (SHM / HGB / dll)
- SPPT PBB (Surat Pemberitahuan Pajak Terhutang Pajak Bumi dan Bangunan)
- STTS / Bukti Lunas PBB
- NPWP — HANYA dari KARTU NPWP FISIK (bukan dari field NPWP di dokumen lain)
- Surat Persetujuan Suami/Istri

INSTRUKSI PENTING:
1. Identifikasi semua jenis dokumen yang ada di SELURUH {$totalPages} halaman
2. Ekstrak HANYA data yang benar-benar tertulis/terlihat jelas di gambar
3. Jika suatu field tidak ada/tidak terbaca, isi dengan string kosong ""
4. Untuk NIK dan No KK: pastikan tepat 16 digit angka, perbaiki karakter OCR umum (O→0, I→1, l→1)
5. Untuk tanggal: gunakan format DD-MM-YYYY atau tulis apa adanya
6. Untuk nama: tulis sesuai di dokumen (HURUF KAPITAL jika memang demikian)
7. Untuk nomor sertifikat tanah: cari label "No." atau "Nomor" di bagian identitas sertifikat, bukan tahun
8. Untuk NOP SPPT: format XX.XX.XXX.XXX.XXX-XXXX.X (biasanya 18 digit dengan titik-strip)
9. KHUSUS NPWP: Ekstrak data NPWP HANYA jika ada KARTU NPWP FISIK (logo npwp/DJP).
   JANGAN ekstrak NPWP dari field "NPWP Wajib Pajak" di dalam STTS, SPPT PBB, atau dokumen pajak lainnya.
10. KHUSUS SURAT KETERANGAN WARIS: Untuk field "Daftar Ahli Waris", tuliskan SETIAP AHLI WARIS DALAM BARIS BARU (dipisahkan karakter newline \n).
    Cantumkan SELURUH DATA DIRI yang ada di dokumen untuk masing-masing ahli waris:
    - Nomor urut & Nama Lengkap
    - NIK (jika tercantum)
    - Tempat & Tanggal Lahir (jika ada)
    - Alamat / Tempat Tinggal (jika ada)
    - Status/Keterangan Khusus yang SANGAT PENTING (misalnya: "(Telah Meninggal Dunia pada tanggal ...)", "(Anak Kandung)", "(Istri/Suami)").
    CONTOH FORMAT DAFTAR AHLI WARIS:
    1. ACENG KURNIA — NIK: 3203..., TTL: Cianjur, 20 Juni 1960, Alamat: Kp. Simpang
    2. EDI KUSWARA (Telah Meninggal Dunia pada tanggal 10 Mei 2015) — NIK: 3203...
    3. ATIK KURNIASIH — NIK: 3203...
    JANGAN menggabungkan daftar ahli waris dalam 1 baris tanpa newline!

Kembalikan HANYA JSON valid dengan struktur berikut:

{
  "dokumen_terdeteksi": ["KTP", "KK"],
  "KTP": {
    "NIK": "",
    "Nama Lengkap": "",
    "Tempat Lahir": "",
    "Tanggal Lahir": "",
    "Jenis Kelamin": "",
    "Golongan Darah": "",
    "Alamat": "",
    "RT": "",
    "RW": "",
    "Kelurahan/Desa": "",
    "Kecamatan": "",
    "Kabupaten/Kota": "",
    "Provinsi": "",
    "Agama": "",
    "Status Perkawinan": "",
    "Pekerjaan": "",
    "Kewarganegaraan": "",
    "Berlaku Hingga": ""
  },
  "KK": {
    "No KK": "",
    "Kepala Keluarga": "",
    "Alamat": "",
    "RT": "",
    "RW": "",
    "Kelurahan/Desa": "",
    "Kecamatan": "",
    "Kabupaten/Kota": "",
    "Provinsi": "",
    "Anggota": [
      {
        "No": "",
        "Nama": "",
        "NIK": "",
        "Jenis Kelamin": "",
        "Tempat Lahir": "",
        "Tanggal Lahir": "",
        "Agama": "",
        "Pendidikan": "",
        "Pekerjaan": "",
        "Status Perkawinan": "",
        "Hubungan Keluarga": "",
        "Kewarganegaraan": ""
      }
    ]
  },
  "Akta Kelahiran": {
    "Nomor Akta Kelahiran": "",
    "Nama Lengkap Anak": "",
    "Tempat Lahir": "",
    "Tanggal Lahir": "",
    "Nama Ayah": "",
    "Nama Ibu": ""
  },
  "Buku Nikah": {
    "Nomor Akta Nikah": "",
    "Nama Suami": "",
    "NIK Suami": "",
    "Nama Istri": "",
    "NIK Istri": "",
    "Tanggal Nikah": "",
    "Tempat Nikah": ""
  },
  "Surat Cerai": {
    "Nomor Putusan": "",
    "Nama Penggugat": "",
    "Nama Tergugat": "",
    "Tanggal Putusan": ""
  },
  "Surat Kematian": {
    "Nama Almarhum": "",
    "Tanggal Meninggal": "",
    "Tempat Meninggal": "",
    "Penyebab": ""
  },
  "Surat Keterangan Waris": {
    "Nomor Surat Waris": "",
    "Nama Pewaris": "",
    "Tanggal Meninggal Pewaris": "",
    "Daftar Ahli Waris": "",
    "Kesimpulan Waris": "",
    "Disahkan Oleh": "",
    "Tanggal Surat": ""
  },
  "Sertifikat Tanah": {
    "Jenis Hak": "",
    "Nomor Sertifikat": "",
    "Nomor Induk Bidang (NIB)": "",
    "Desa/Kelurahan": "",
    "Kecamatan": "",
    "Kabupaten/Kota": "",
    "Provinsi": "",
    "Luas Tanah (m2)": "",
    "Atas Nama": "",
    "Tanggal Terbit": ""
  },
  "SPPT PBB": {
    "Nomor Objek Pajak (NOP)": "",
    "Tahun Pajak": "",
    "Nama Wajib Pajak": "",
    "Alamat Wajib Pajak": "",
    "Letak Objek Pajak": "",
    "Luas Tanah (m2)": "",
    "Luas Bangunan (m2)": "",
    "Nilai Jual Objek Pajak (NJOP)": "",
    "PBB Terutang": ""
  },
  "Bukti Lunas PBB (STTS)": {
    "Nomor Objek Pajak (NOP)": "",
    "Tahun Pajak": "",
    "Nama Wajib Pajak": "",
    "Jumlah Bayar": "",
    "Tanggal Bayar": "",
    "Bank/Tempat Bayar": "",
    "Nomor Transaksi": ""
  },
  "NPWP": {
    "No NPWP": "",
    "Nama Terdaftar": "",
    "Alamat": "",
    "KPP": ""
  },
  "Surat Persetujuan Suami/Istri": {
    "Nama Pemberi Persetujuan": "",
    "NIK Pemberi Persetujuan": "",
    "Hubungan": "",
    "Nama Penerima Persetujuan": "",
    "Keterangan": "",
    "Tanggal Surat": ""
  }
}

PENTING:
- "dokumen_terdeteksi" berisi semua dokumen yang ditemukan di SELURUH {$totalPages} halaman
- Jika tidak ada dokumen yang dikenal sama sekali, kembalikan {"dokumen_terdeteksi": [], "catatan": "..."}
- NPWP hanya dimasukkan jika ada KARTU NPWP FISIK (logo npwp/DJP), bukan sekedar field NPWP di dokumen lain
PROMPT;
    }
}
