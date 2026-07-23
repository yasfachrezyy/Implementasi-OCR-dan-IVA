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
            $totalPages = preg_match_all("/\/Type\s*\/Page[^s]/i", $pdfContent, $dummy);
            
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
        ini_set('memory_limit', '1024M');
        set_time_limit(120);

        $tempFiles = [];
        try {
            $filePath = storage_path('app/public/' . str_replace('public/', '', $request->file_path));
            $pageNumber = $request->page_number;

            $tempDir = storage_path('app/public/temp_ocr');
            if (!File::exists($tempDir)) File::makeDirectory($tempDir, 0755, true);

            $baseImage = $tempDir . '/page_' . $pageNumber . '_' . time();
            $imagePath = $baseImage . '.jpg';
            $tempFiles[] = $imagePath;

            // 1. RASTERISASI PDF 1 HALAMAN PENUH
            $gsCommand = "gswin64c -dNOPAUSE -dBATCH -sDEVICE=jpeg -r300 -dFirstPage={$pageNumber} -dLastPage={$pageNumber} -sOutputFile=\"{$imagePath}\" \"{$filePath}\" 2>&1";
            exec($gsCommand, $output, $returnVar);

            if (!File::exists($imagePath)) {
                throw new \Exception("Ghostscript gagal melakukan rasterisasi dokumen.");
            }

            // 2. IN-PLACE IMAGE PROCESSING
            $img = @imagecreatefromjpeg($imagePath);
            if (!$img) throw new \Exception("Gagal memuat gambar utama.");

            imagefilter($img, IMG_FILTER_GRAYSCALE);
            
            $normalImagePath = $baseImage . '_normal.jpg';
            $tempFiles[] = $normalImagePath;
            imagejpeg($img, $normalImagePath, 100);

            imagefilter($img, IMG_FILTER_BRIGHTNESS, 60); 
            imagefilter($img, IMG_FILTER_CONTRAST, -20);
            
            $brightImagePath = $baseImage . '_bright.jpg';
            $tempFiles[] = $brightImagePath;
            imagejpeg($img, $brightImagePath, 100);

            imagedestroy($img);
            gc_collect_cycles(); 

            // 3. OCR HALAMAN PENUH SECARA PARALEL
            $textNormal = (new TesseractOCR($normalImagePath))->lang('ind', 'eng')->run();
            $textBright = (new TesseractOCR($brightImagePath))->lang('ind', 'eng')->psm(4)->run();

            // 4. SEGMENTASI & PENGURUTAN DINAMIS BERDASARKAN POSISI PDF
            $tempExtracted = [];

            // A. ISOLASI & DETEKSI KTP
            $ktpSegment = $textBright;
            if (preg_match('/KARTU\s*KELUARGA/i', $ktpSegment, $m, PREG_OFFSET_CAPTURE)) {
                $offset = $m[0][1];
                $part1 = substr($ktpSegment, 0, $offset);
                $part2 = substr($ktpSegment, $offset);
                $ktpSegment = preg_match('/PROVINSI/i', $part1) ? $part1 : $part2;
            }
            if (preg_match('/PROVINSI|NIK|PENDUDUK/i', $ktpSegment)) {
                $dataKtp = $this->extractDataFromText($ktpSegment, 'KTP');
                if (!empty($dataKtp['KTP']['NIK']) || !empty($dataKtp['KTP']['Nama Lengkap'])) {
                    preg_match('/PROVINSI|NIK/i', $textBright, $m, PREG_OFFSET_CAPTURE);
                    $pos = isset($m[0][1]) ? $m[0][1] : 1001;
                    while (isset($tempExtracted[$pos])) $pos++;
                    $tempExtracted[$pos] = $dataKtp;
                }
            }

            // B. ISOLASI & DETEKSI KK
            $kkSegment = $textNormal;
            if (preg_match('/KARTU\s*KELUARGA/i', $kkSegment, $m, PREG_OFFSET_CAPTURE)) {
                $kkSegment = substr($kkSegment, $m[0][1]);
                if (preg_match('/PROVINSI\s*JAWA/i', $kkSegment, $m2, PREG_OFFSET_CAPTURE)) {
                    if ($m2[0][1] > 50) { 
                        $kkSegment = substr($kkSegment, 0, $m2[0][1]);
                    }
                }
            }
            if (preg_match('/KARTU\s*KELUARGA|Kepala Keluarga/i', $kkSegment)) {
                $dataKk = $this->extractDataFromText($kkSegment, 'KK');
                if (!empty($dataKk['Kartu Keluarga']['No KK']) || !empty($dataKk['Kartu Keluarga']['Kepala Keluarga'])) {
                    preg_match('/KARTU\s*KELUARGA/i', $textNormal, $m, PREG_OFFSET_CAPTURE);
                    $pos = isset($m[0][1]) ? $m[0][1] : 2002;
                    while (isset($tempExtracted[$pos])) $pos++;
                    $tempExtracted[$pos] = $dataKk;
                }
            }

            // C. DETEKSI DOKUMEN LAINNYA SECARA DINAMIS
            $docTypes = [
                'NPWP' => ['Pola' => 'NPWP|DIREKTORAT JENDERAL PAJAK', 'Key' => 'NPWP'],
                'SERTIFIKAT' => ['Pola' => 'SERTIPIKAT|BADAN PERTANAHAN|HAK MILIK', 'Key' => 'Sertifikat Tanah'],
                'SPPT' => ['Pola' => 'SURAT PEMBERITAHUAN PAJAK|SPPT|NOP', 'Key' => 'SPPT PBB'],
                'WARIS' => ['Pola' => 'KETERANGAN WARIS|AHLI WARIS', 'Key' => 'Surat Keterangan Waris'],
                'NIKAH' => ['Pola' => 'AKTA NIKAH|BUKU NIKAH', 'Key' => 'Akta Nikah'],
                'KELAHIRAN' => ['Pola' => 'AKTA KELAHIRAN|KUTIPAN AKTA KELAHIRAN', 'Key' => 'Akta Kelahiran'],
                'KEMATIAN' => ['Pola' => 'AKTA KEMATIAN', 'Key' => 'Akta Kematian'],
                'PAJAK_DAERAH' => ['Pola' => 'BPHTB|SSP|SURAT SETORAN PAJAK', 'Key' => 'Bukti Bayar Pajak']
            ];

            $fallbackPos = 3000;
            foreach ($docTypes as $type => $config) {
                $data = $this->extractDataFromText($textNormal, $type);
                if (!empty($data[$config['Key']])) {
                    preg_match('/' . $config['Pola'] . '/i', $textNormal, $m, PREG_OFFSET_CAPTURE);
                    $pos = isset($m[0][1]) ? $m[0][1] : $fallbackPos;
                    while (isset($tempExtracted[$pos])) $pos++;
                    $tempExtracted[$pos] = $data;
                    $fallbackPos += 10;
                }
            }

            ksort($tempExtracted);
            $extractedData = [];
            foreach ($tempExtracted as $data) {
                $extractedData = array_merge($extractedData, $data);
            }

            // Bersihkan file sementara
            foreach ($tempFiles as $tmp) {
                if (File::exists($tmp)) File::delete($tmp);
            }

            return response()->json([
                'status' => 'success',
                'page' => $pageNumber,
                'data' => $extractedData 
            ]);

        } catch (\Exception $e) {
            foreach ($tempFiles as $tmp) {
                if (File::exists($tmp)) File::delete($tmp);
            }
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
        $lines = explode("\n", $text);

        // 1. DETEKSI KTP 
        if ($docType === 'KTP') {
            $ktpNik = '';
            if (preg_match('/(?:NIK|N1K)[\W_]*([0-9a-zA-Z\s]{16,30})/i', $text, $m)) {
                $ktpNik = substr(preg_replace('/[^0-9]/', '', $this->fixOcrNumbers($m[1])), 0, 16);
            }
            if (empty($ktpNik)) {
                foreach ($lines as $line) {
                    if (preg_match('/PROVINSI|KABUPATEN|KOTA|KARTU|PENDUDUK/i', $line)) continue;
                    $lineClean = preg_replace('/[^0-9]/', '', $this->fixOcrNumbers($line));
                    if (strlen($lineClean) >= 16) {
                        $ktpNik = substr($lineClean, 0, 16);
                        break;
                    }
                }
            }

            $nama = $this->matchBetween($text, ['Nama', 'Narna', 'Noma', 'Name'], ['Tempat', 'Lahir', 'Tgl', 'Ternpat', 'Jenis']);
            if (!$nama) $nama = $this->matchRegex('/(?:Nama|Narna|Name)\s*[:;\|\.]?\s*([A-Za-z\s\.\,\']+)/i', $text);
            $nama = trim(preg_replace('/(Tempat|Lahir|Jenis|Kelamin|Gol).*$/i', '', $nama));

            $ttl = $this->matchBetween($text, ['Lahir', 'Tgl', 'Tempat', 'Ternpat'], ['Jenis', 'Kelamin', 'Keiamin', 'Gol', 'Darah', 'Alamat']);
            if (!$ttl || !preg_match('/\d/', $ttl)) {
                if (preg_match('/([A-Za-z\s]+)[\,\|\:]?\s*(\d{2}[\-\.]\d{2}[\-\.]\d{4})/i', $text, $m)) {
                    $kota = trim(preg_replace('/.*(?:Tempat|Lahir|Tgl)[\W_]*/i', '', $m[1]));
                    $ttl = $kota . ', ' . str_replace('.', '-', $m[2]);
                }
            }

            $jk = '';
            if (preg_match('/(LAKI[\s\-]*LAKI|PEREMPUAN)/i', $text, $m)) {
                $jk = str_replace([' ', 'LAKILAKI'], 'LAKI-LAKI', strtoupper($m[1]));
            }

            $alamat = $this->matchBetween($text, ['Alamat', 'Aiamat'], ['RT', 'RW', 'Kel', 'Desa', 'Kecamatan']);
            if (!$alamat) $alamat = $this->matchRegex('/(?:Alamat|Aiamat)\s*[:;\|\.]?\s*([A-Za-z0-9\s\.\,\']+)/i', $text);

            $agama = '';
            if (preg_match('/(ISLAM|KRISTEN|KATHOLIK|HINDU|BUDDHA|KONGHUCU)/i', $text, $m)) {
                $agama = strtoupper($m[1]);
            }

            $pekerjaan = $this->matchBetween($text, ['Pekerjaan', 'Pekerja', 'Pekerjaon'], ['Kewarganegaraan', 'WNI', 'WNA', 'Berlaku']);
            if (!$pekerjaan) $pekerjaan = $this->matchRegex('/(?:Pekerjaan|Pekerja)\s*[:;\|\.]?\s*([A-Za-z0-9\s\.\,\'\/]+)/i', $text);
            $pekerjaan = trim(preg_replace('/(Kewarganegaraan|WNI|WNA).*$/i', '', $pekerjaan));

            $results['KTP'] = [
                'NIK' => $ktpNik,
                'Nama Lengkap' => $nama,
                'Tempat/Tgl Lahir' => $ttl,
                'Jenis Kelamin' => $jk,
                'Alamat' => $alamat,
                'Agama' => $agama,
                'Pekerjaan' => $pekerjaan,
            ];
        }

        // 2. DETEKSI KARTU KELUARGA (KK)
        if ($docType === 'KK') {
            if (preg_match('/KARTU KELUARGA/i', $text) || preg_match('/Nama Kepala/i', $text)) {
                $noKk = '';
                if (preg_match('/(?:No|Nomor|N0)[\W_]*([0-9a-zA-Z\s]{16,25})/i', $text, $m)) {
                    $rawKk = preg_replace('/[^0-9]/', '', $this->fixOcrNumbers($m[1]));
                    if (strlen($rawKk) > 16) {
                        $noKk = substr($rawKk, 0, 8) . substr($rawKk, -8);
                    } else {
                        $noKk = substr($rawKk, 0, 16);
                    }
                }
                
                if (empty($noKk)) {
                    foreach ($lines as $line) {
                        if (preg_match('/KARTU|KELUARGA|KEPALA|NAMA/i', $line)) continue;
                        $lineClean = preg_replace('/[^0-9]/', '', $this->fixOcrNumbers($line));
                        if (strlen($lineClean) >= 16) {
                            if (strlen($lineClean) > 16 && strlen($lineClean) < 25) {
                                $noKk = substr($lineClean, 0, 8) . substr($lineClean, -8);
                            } else {
                                $noKk = substr($lineClean, 0, 16);
                            }
                            break;
                        }
                    }
                }

                $kkData = [
                    'No KK' => $noKk,
                    'Kepala Keluarga' => $this->matchBetween($text, ['Kepala Keluarga'], ['Alamat', 'RT', 'RW', 'Desa', 'Kelurahan']),
                ];

                if (preg_match('/(.*?)\s*(Desa|Kelurahan|RT|RW|Alamat)/i', $kkData['Kepala Keluarga'], $m)) {
                    $kkData['Kepala Keluarga'] = trim($m[1]);
                }

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
                        $name = ltrim($name, '|1l!][');
                        $name = preg_replace('/^[JjIi]\s+/', '', $name); 
                        if (preg_match('/^[JjIi][A-Z]/', $name)) {
                            $name = substr($name, 1); 
                        }
                        $name = trim(preg_replace('/[^A-Za-z\s\.\']/', '', $name));

                        if (empty($name) || strlen($name) < 2) $name = "Nama tidak terbaca";

                        $kkData['Anggota ' . $anggotaCount] = $name . ' - ' . $nik;
                        $anggotaCount++;
                    }
                }
                $results['Kartu Keluarga'] = $kkData;
            }
        }

        if ($docType === 'NPWP') {
            if (preg_match('/NPWP|DIREKTORAT JENDERAL PAJAK|KPP/i', $text)) {
                $results['NPWP'] = [
                    'No NPWP' => $this->matchRegex('/(\d{2}[\.\s]?\d{3}[\.\s]?\d{3}[\.\s]?\d{1}[\-\s]?\d{3}[\.\s]?\d{3})/i', $text) ?: $this->matchRegex('/NPWP\s*[:;]?\s*([\d\.\-]+)/i', $text),
                    'Nama Terdaftar' => $this->matchRegex('/Nama\s*[:;]?\s*([A-Za-z\s\.,]+)/i', $text),
                ];
            }
        }

        if ($docType === 'SERTIFIKAT') {
            if (preg_match('/SERTIPIKAT|BADAN PERTANAHAN|HAK MILIK|HAK GUNA BANGUNAN/i', $text)) {
                $results['Sertifikat Tanah'] = [
                    'Jenis Hak' => $this->matchRegex('/(HAK MILIK|HAK GUNA BANGUNAN|HAK GUNA USAHA|HAK PAKAI)/i', $text),
                    'Nomor Induk Bidang (NIB)' => $this->matchRegex('/NIB\s*[:;]?\s*([\d\.\-]+)/i', $text),
                    'Kabupaten/Kota' => $this->matchRegex('/(?:Kabupaten|Kota)\s*[:;]?\s*([A-Za-z\s]+)/i', $text)
                ];
            }
        }

        if ($docType === 'SPPT') {
            if (preg_match('/SURAT PEMBERITAHUAN PAJAK|SPPT|PAJAK BUMI/i', $text)) {
                $results['SPPT PBB'] = [
                    'Nomor Objek Pajak (NOP)' => preg_replace('/[^0-9\.\-]/', '', $this->matchRegex('/NOP\s*[:;]?\s*([\d\.\-\s]+)/i', $text)),
                    'Nama Wajib Pajak' => $this->matchRegex('/NAMA WAJIB PAJAK\s*[:;]?\s*([A-Za-z\s\.,]+)/i', $text),
                    'Tahun Pajak' => $this->matchRegex('/TAHUN\s*[:;]?\s*(\d{4})/i', $text)
                ];
            }
        }

        if ($docType === 'WARIS') {
            if (preg_match('/KETERANGAN WARIS|AHLI WARIS|KETERANGAN HAK MEWARIS/i', $text)) {
                $results['Surat Keterangan Waris'] = [
                    'Nama Pewaris / Almarhum' => $this->matchRegex('/(?:Almarhum|Almarhumah|Pewaris)\s*[:;]?\s*([A-Za-z\s\.,]+)/i', $text),
                    'Keterangan Tambahan' => 'Pastikan cek daftar ahli waris secara manual pada dokumen'
                ];
            }
        }

        if ($docType === 'NIKAH') {
            if (preg_match('/AKTA NIKAH|BUKU NIKAH|KEMENTERIAN AGAMA/i', $text)) {
                $results['Akta Nikah'] = [
                    'Nomor Akta' => $this->matchRegex('/Nomor\s*[:;]?\s*([0-9\/\-]+)/i', $text),
                    'Nama Suami' => $this->matchRegex('/(?:Suami|Pria)\s*[:;]?\s*([A-Za-z\s\.,]+)/i', $text),
                    'Nama Istri' => $this->matchRegex('/(?:Istri|Isteri|Wanita)\s*[:;]?\s*([A-Za-z\s\.,]+)/i', $text)
                ];
            }
        }

        if ($docType === 'KELAHIRAN') {
            if (preg_match('/AKTA KELAHIRAN|KUTIPAN AKTA KELAHIRAN/i', $text)) {
                $results['Akta Kelahiran'] = [
                    'Nama Anak' => $this->matchRegex('/bahwa di.*?telah lahir:\s*([A-Za-z\s]+)/i', $text) ?: $this->matchRegex('/Nama\s*[:;]?\s*([A-Za-z\s\.,]+)/i', $text),
                    'Nama Orang Tua' => 'Pengecekan manual direkomendasikan'
                ];
            }
        }

        if ($docType === 'KEMATIAN') {
            if (preg_match('/AKTA KEMATIAN|SURAT KETERANGAN KEMATIAN/i', $text)) {
                $results['Akta Kematian'] = [
                    'Nama Almarhum' => $this->matchRegex('/(?:telah meninggal dunia|nama)\s*[:;]?\s*([A-Za-z\s\.,]+)/i', $text),
                    'Tanggal Meninggal' => $this->matchRegex('/tanggal\s*([0-9]{1,2}\s+[A-Za-z]+\s+[0-9]{4})/i', $text)
                ];
            }
        }

        if ($docType === 'PAJAK_DAERAH') {
            if (preg_match('/BPHTB|SSP|SURAT SETORAN PAJAK|PAJAK DAERAH/i', $text)) {
                $results['Bukti Bayar Pajak'] = [
                    'Jenis Pajak' => preg_match('/BPHTB/i', $text) ? 'BPHTB' : 'SSP / Lainnya',
                    'Nama Penyetor' => $this->matchRegex('/Nama\s*Wajib\s*Pajak\s*[:;]?\s*([A-Za-z\s\.,]+)/i', $text),
                    'Nominal' => $this->matchRegex('/Rp\s*[:;\.]?\s*([0-9\.]+)/i', $text)
                ];
            }
        }

        return $results;
    }

    // =========================================================================
    // FUNGSI PEMBANTU (HELPER)
    // =========================================================================

    private function matchBetween($text, $startWords, $endWords) {
        $start = implode('|', $startWords);
        $end = implode('|', $endWords);
        
        if (preg_match('/(?:' . $start . ')[^\wA-Za-z]*([\s\S]*?)(?=(?:' . $end . ')|$)/i', $text, $matches)) {
            $result = trim(preg_replace('/[^A-Za-z0-9\s\.,\-\/]/', '', $matches[1]));
            return preg_replace('/\s+/', ' ', $result); 
        }
        return '';
    }

    private function matchRegex($pattern, $text) {
        if (preg_match($pattern, $text, $matches)) {
            return trim($matches[1]);
        }
        return '';
    }

    private function fixOcrNumbers($text) {
        return str_replace(
            ['O', 'o', 'l', 'L', 'I', 'i', 'S', 's', '|', 'B'], 
            ['0', '0', '1', '1', '1', '1', '5', '5', '1', '8'], 
            $text
        );
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