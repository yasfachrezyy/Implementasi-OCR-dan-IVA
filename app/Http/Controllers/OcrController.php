<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use thiagoalessio\TesseractOCR\TesseractOCR;
use Illuminate\Support\Facades\File;

class OcrController extends Controller
{
    public function countPages(Request $request)
    {
        try {
            $filePath = storage_path('app/public/' . str_replace('public/', '', $request->file_path));
            
            if (!File::exists($filePath)) {
                return response()->json(['status' => 'error', 'message' => 'File fisik PDF tidak ditemukan di: ' . $filePath], 404);
            }

            $pdfContent = file_get_contents($filePath);
            $totalPages = preg_match_all("/\/Page\W/", $pdfContent, $dummy);
            
            if ($totalPages === 0) {
                $totalPages = 1; 
            }

            return response()->json([
                'status' => 'success',
                'total_pages' => $totalPages
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function processSinglePage(Request $request)
    {
        try {
            $filePath = storage_path('app/public/' . str_replace('public/', '', $request->file_path));
            $pageNumber = $request->page_number;

            $tempDir = storage_path('app/public/temp_ocr');
            if (!File::exists($tempDir)) File::makeDirectory($tempDir, 0755, true);

            $imagePath = $tempDir . '/page_' . $pageNumber . '_' . time() . '.jpg';

            // 1. RASTERISASI PDF
            $gsCommand = "gswin64c -dNOPAUSE -dBATCH -sDEVICE=jpeg -r300 -dFirstPage={$pageNumber} -dLastPage={$pageNumber} -sOutputFile=\"{$imagePath}\" \"{$filePath}\" 2>&1";
            exec($gsCommand, $output, $returnVar);

            if (!File::exists($imagePath)) {
                throw new \Exception("Ghostscript gagal melakukan rasterisasi dokumen.");
            }

            // 2. PHP GD GRAYSCALE (Aman & Stabil, membuat OCR lebih mudah membaca)
            $img = @imagecreatefromjpeg($imagePath);
            if ($img) {
                imagefilter($img, IMG_FILTER_GRAYSCALE);
                imagefilter($img, IMG_FILTER_CONTRAST, -20);
                imagejpeg($img, $imagePath, 100);
                imagedestroy($img);
            }

            // 3. OCR SELURUH HALAMAN (Membaca semua teks sekaligus)
            $ocrText = (new TesseractOCR($imagePath))
                ->lang('ind', 'eng')
                ->run();

            // =====================================================================
            // 4. LOGICAL TEXT SPLITTING (Solusi KTP & KK Tertukar)
            // =====================================================================
            $textKtp = $ocrText;
            $textKk = $ocrText;

            // Jika dalam teks ditemukan KTP dan KK sekaligus, belah teksnya jadi dua!
            if (preg_match('/KARTU KELUARGA/i', $ocrText)) {
                $parts = preg_split('/KARTU KELUARGA/i', $ocrText, 2);
                if (count($parts) === 2) {
                    $textKtp = $parts[0]; // Semua teks di atas judul KK (Area KTP)
                    $textKk = "KARTU KELUARGA\n" . $parts[1]; // Semua teks di bawahnya (Area KK)
                }
            }

            // 5. EKSTRAKSI TERPISAH
            // Area atas dilempar ke mesin KTP, area bawah dilempar ke mesin KK
            $dataKtp = $this->extractDataFromText($textKtp, 'KTP');
            $dataKk = $this->extractDataFromText($textKk, 'KK');
            
            // Untuk SPPT/NPWP/Sertifikat, biarkan membaca teks utuh
            $dataLain = $this->extractDataFromText($ocrText, 'LAINNYA'); 

            // Gabungkan hasilnya
            $extractedData = array_merge($dataKtp, $dataKk, $dataLain);

            // Hapus file gambar sementara
            File::delete($imagePath);

            return response()->json([
                'status' => 'success',
                'page' => $pageNumber,
                'data' => $extractedData 
            ]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function saveVerifiedData(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil disimpan.'
        ]);
    }

    private function extractDataFromText($text, $docType): array
    {
        $results = [];
        $textClean = str_replace(['O', 'l', 'I', 'S', '|'], ['0', '1', '1', '5', '1'], $text);
        $lines = explode("\n", $text);

        $ktpNik = '';
        $noKk = '';

        // --- 1. DETEKSI KTP ---
        if ($docType === 'KTP') {
            $scoreKTP = 0;
            if (preg_match('/PROVINSI/i', $text)) $scoreKTP++;
            if (preg_match('/KABUPATEN|KOTA/i', $text)) $scoreKTP++;
            if (preg_match('/NIK/i', $text)) $scoreKTP++;
            
            if ($scoreKTP >= 1 || preg_match('/KARTU TANDA PENDUDUK/i', $text)) {
                $textForNik = $this->fixOcrNumbers($text);
                $ktpNik = $this->matchRegex('/(?<!\d)(\d{16})(?!\d)/', $textForNik);
                
                if (!$ktpNik && preg_match('/NIK\s*[:;\.\s]*([0-9\sOIlS\|]{16,30})/i', $text, $ktpNikMatch)) {
                    $ktpNik = substr(preg_replace('/[^0-9]/', '', $this->fixOcrNumbers($ktpNikMatch[1])), 0, 16);
                }

                $results['KTP'] = [
                    'NIK' => $ktpNik,
                    'Nama Lengkap' => $this->matchRegex('/Nama\s*[:;\.]?\s*([A-Za-z\s\.,]+?)(?=\n|Tempat|Lahir|$)/i', $text),
                    'Tempat/Tgl Lahir' => $this->matchRegex('/Lahir\s*[:;\.]?\s*([A-Za-z\s\.,\-0-9]+)/i', $text),
                    'Jenis Kelamin' => $this->matchRegex('/Kelamin\s*[:;\.]?\s*([A-Za-z]+)/i', $text),
                    'Alamat' => $this->matchRegex('/Alamat\s*[:;\.]?\s*([A-Za-z\s\.,0-9]+?)(?=\n|RT|RW|$)/i', $text),
                    'Agama' => $this->matchRegex('/Agama\s*[:;\.]?\s*([A-Za-z]+)/i', $text),
                    'Pekerjaan' => $this->matchRegex('/Pekerjaan\s*[:;\.]?\s*([A-Za-z\s]+)/i', $text),
                ];
            }
        }

        // --- 2. DETEKSI KARTU KELUARGA (KK) ---
        if ($docType === 'KK') {
            if (preg_match('/KARTU KELUARGA/i', $text) || preg_match('/Nama Kepala Keluarga/i', $text)) {
                
                preg_match('/No\.\s*([A-Za-z0-9\s\|]{16,25})/i', $text, $noMatch);
                if (!empty($noMatch[1])) {
                    $noKk = preg_replace('/[^0-9]/', '', $this->fixOcrNumbers($noMatch[1]));
                    if (strlen($noKk) > 16) $noKk = substr($noKk, 0, 16);
                }

                $kkData = [
                    'No KK' => $noKk,
                    'Kepala Keluarga' => $this->matchRegex('/Kepala Keluarga\s*[:;\.]?\s*([A-Za-z\s\.,]+?)(?=\s{2,}|\n|$|Alamat|RT|Desa|Kelurahan)/i', $text),
                ];

                $anggotaCount = 1;
                $extractedNiks = [];

                foreach ($lines as $line) {
                    if (preg_match('/NIP|Tangan|Cap|Kades/i', $line)) continue;

                    $lineClean = $this->fixOcrNumbers($line);
                    if (preg_match('/(?<!\d)(\d{16})(?!\d)/', $lineClean, $match)) {
                        $nik = $match[1];
                        
                        if ($nik === $noKk) continue;
                        if (in_array($nik, $extractedNiks)) continue;

                        $extractedNiks[] = $nik;

                        $pos = strpos($lineClean, $nik);
                        $teksSebelumNik = substr($line, 0, $pos);
                        $name = preg_replace('/^[\d\W_]+/', '', $teksSebelumNik);
                        $name = trim(preg_replace('/[^A-Za-z\s\.\']/', '', $name));

                        if (empty($name) || strlen($name) < 2) $name = "Nama tidak terbaca";

                        $kkData['Anggota ' . $anggotaCount] = $name . ' - ' . $nik;
                        $anggotaCount++;
                    }
                }
                $results['Kartu Keluarga'] = $kkData;
            }
        }

        // --- 3. DETEKSI DOKUMEN LAIN ---
        if ($docType === 'LAINNYA') {
            if (preg_match('/NPWP/i', $text) || preg_match('/DIREKTORAT JENDERAL PAJAK/i', $text)) {
                $results['NPWP'] = [
                    'No NPWP' => $this->matchRegex('/(\d{2}[\.\s]?\d{3}[\.\s]?\d{3}[\.\s]?\d{1}[\-\s]?\d{3}[\.\s]?\d{3})/i', $text) ?: $this->matchRegex('/NPWP\s*[:;]?\s*([\d\.\-]+)/i', $text),
                    'Nama Lengkap' => $this->matchRegex('/Nama\s*[:;]?\s*([A-Za-z\s\.,]+)/i', $text),
                ];
            }

            if (preg_match('/SPPT/i', $text) || preg_match('/PAJAK BUMI DAN BANGUNAN/i', $text)) {
                $results['SPPT PBB'] = [
                    'NOP (Nomor Objek Pajak)' => $this->matchRegex('/NOP\s*[:;]?\s*([\d\.\-]+)/i', $text),
                    'Nama Wajib Pajak' => $this->matchRegex('/NAMAWP\s*[:;]?\s*([A-Za-z\s\.,]+)/i', $text) ?: $this->matchRegex('/Wajib Pajak\s*[:;]?\s*([A-Za-z\s\.,]+)/i', $text),
                    'Luas Bumi (m2)' => $this->matchRegex('/Bumi\s+(\d+)/i', $text),
                    'Luas Bangunan (m2)' => $this->matchRegex('/Bangunan\s+(\d+)/i', $text),
                ];
            }

            if (preg_match('/BADAN PERTANAHAN NASIONAL/i', $text) || preg_match('/SERTIPIKAT/i', $text)) {
                $results['Sertifikat Tanah'] = [
                    'Jenis Hak' => $this->matchRegex('/Hak\s*(Milik|Guna Bangunan|Pakai|Guna Usaha)/i', $text),
                    'NIB' => $this->matchRegex('/NIB\s*[:;]?\s*([\d\.]+)/i', $text),
                    'Pemegang Hak' => $this->matchRegex('/Pemegang Hak\s*[:;]?\s*([A-Za-z\s\.,]+)/i', $text) ?: $this->matchRegex('/Nama\s*Pemegang\s*Hak\s*[:;]?\s*([A-Za-z\s\.,]+)/i', $text),
                    'Desa / Kelurahan' => $this->matchRegex('/Desa\/Kelurahan\s*[:;]?\s*([A-Za-z\s\.,]+)/i', $text),
                ];
            }
        }

        return $results;
    }

    private function matchRegex($pattern, $text) {
        if (preg_match($pattern, $text, $matches)) {
            return trim($matches[1]);
        }
        return '';
    }

    private function fixOcrNumbers($text) {
        return str_replace(['O', 'o', 'l', 'I', 'i', 'S', 's', '|'], ['0', '0', '1', '1', '1', '5', '5', '1'], $text);
    }

    public function viewPdf(Request $request)
    {
        $filePath = $request->query('path'); 
        $fullPath = storage_path('app/public/' . str_replace('public/', '', $filePath));

        if (!\Illuminate\Support\Facades\File::exists($fullPath)) {
            abort(404, 'File tidak ditemukan di: ' . $fullPath);
        }
        
        return response()->file($fullPath);
    }
}