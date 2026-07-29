<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Models\Permohonan;
use App\Services\GeminiOcrService;

class OcrController extends Controller
{
    private function gsPath(): string
    {
        $userAppData = getenv('LOCALAPPDATA') ? getenv('LOCALAPPDATA') . '\Programs' : '';

        $scanDirs = [
            'C:\Program Files\gs',
            'C:\tools\Ghostscript',
            'C:\tools',
            $userAppData . '\Ghostscript',
            $userAppData,
        ];

        foreach ($scanDirs as $dir) {
            if ($dir && is_dir($dir)) {
                foreach (array_reverse(glob($dir . '\*\bin\gswin64c.exe') ?: []) as $p) {
                    if (file_exists($p)) return $p;
                }
                foreach (array_reverse(glob($dir . '\bin\gswin64c.exe') ?: []) as $p) {
                    if (file_exists($p)) return $p;
                }
                foreach (array_reverse(glob($dir . '\gswin64c.exe') ?: []) as $p) {
                    if (file_exists($p)) return $p;
                }
            }
        }

        $staticPaths = [
            $userAppData . '\Ghostscript\bin\gswin64c.exe',
            'C:\Program Files\gs\gs10.04.0\bin\gswin64c.exe',
            'C:\Program Files\gs\gs10.03.1\bin\gswin64c.exe',
            'C:\Program Files\gs\gs10.02.1\bin\gswin64c.exe',
            'C:\Program Files\gs\gs10.01.2\bin\gswin64c.exe',
            'C:\Program Files\gs\gs10.00.0\bin\gswin64c.exe',
            'C:\Program Files\gs\gs9.56.1\bin\gswin64c.exe',
        ];
        foreach ($staticPaths as $p) {
            if ($p && file_exists($p)) return $p;
        }

        return 'gswin64c';
    }

    private function isExecutableAvailable(string $cmd): bool
    {
        if (file_exists($cmd)) return true;
        $out = @shell_exec('where ' . escapeshellarg($cmd) . ' 2>&1');
        return !empty($out)
            && !str_contains(strtolower($out), 'could not find')
            && !str_contains(strtolower($out), 'error');
    }

    //hitung halaman
    public function countPages(Request $request)
    {
        try {
            $filePath = storage_path('app/public/' . str_replace('public/', '', $request->file_path));

            if (!File::exists($filePath)) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'File PDF tidak ditemukan di: ' . $filePath,
                ], 404);
            }

            $totalPages = $this->getPdfPageCount($filePath);

            return response()->json([
                'status'      => 'success',
                'total_pages' => $totalPages,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    private function getPdfPageCount(string $filePath): int
    {
        $gs = $this->gsPath();

        //metode ghostscript
        $gsCmd = "\"{$gs}\" -q -dNODISPLAY --permit-file-read=\"{$filePath}\" -c \"({$filePath}) (r) file runpdfbegin pdfpagecount = quit\" 2>&1";
        $output = shell_exec($gsCmd);
        if ($output && is_numeric(trim($output))) {
            return max(1, (int) trim($output));
        }

        //fallback regex
        $pdfContent = file_get_contents($filePath);
        $count = preg_match_all('/\/Type\s*\/Page[^s]/i', $pdfContent, $dummy);
        return $count > 0 ? $count : 1;
    }

    //proses
    public function processSinglePage(Request $request)
    {
        ini_set('memory_limit', '-1');
        set_time_limit(120);

        $tempFiles = [];

        try {
            $filePath   = storage_path('app/public/' . str_replace('public/', '', $request->file_path));
            $pageNumber = (int) $request->page_number;
            $gs         = $this->gsPath();

            if (!$this->isExecutableAvailable($gs)) {
                throw new \Exception('Ghostscript (gswin64c) belum terinstall atau tidak ditemukan di PATH.');
            }

            $geminiKey = env('GEMINI_API_KEY', '');
            if (empty($geminiKey)) {
                throw new \Exception('GEMINI_API_KEY belum dikonfigurasi di file .env');
            }

            //rasterisasi PDF → JPEG (300 DPI)
            $tempDir = storage_path('app/public/temp_ocr');
            if (!File::exists($tempDir)) File::makeDirectory($tempDir, 0755, true);

            $baseName  = $tempDir . '/page_' . $pageNumber . '_' . time() . '_' . rand(1000, 9999);
            $jpgPath   = $baseName . '.jpg';
            $tempFiles[] = $jpgPath;

            $gsCommand = "\"{$gs}\" -dNOPAUSE -dBATCH -sDEVICE=jpeg -r300 -dJPEGQ=95 "
                . "-dFirstPage={$pageNumber} -dLastPage={$pageNumber} "
                . "-sOutputFile=\"{$jpgPath}\" \"{$filePath}\" 2>&1";

            exec($gsCommand, $gsOutput, $gsReturnVar);

            if (!File::exists($jpgPath)) {
                $gsLog = implode("\n", $gsOutput);
                throw new \Exception('Ghostscript gagal render halaman. Log: ' . $gsLog);
            }

            $geminiService = new GeminiOcrService();
            $rawExtractedData = $geminiService->extractFromImage($jpgPath, $pageNumber);

            $extractedData = $this->handleDuplicateDocuments($rawExtractedData);

            foreach ($tempFiles as $tmp) {
                if (File::exists($tmp)) File::delete($tmp);
            }

            return response()->json([
                'status' => 'success',
                'page'   => $pageNumber,
                'data'   => $extractedData,
            ]);

        } catch (\Throwable $e) {
            foreach ($tempFiles as $tmp) {
                if (File::exists($tmp)) File::delete($tmp);
            }
            \Illuminate\Support\Facades\Log::error("OCR Error halaman {$request->page_number}: " . $e->getMessage());
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function processDocument(Request $request)
    {
        ini_set('memory_limit', '-1');
        set_time_limit(300);

        $tempFiles = [];

        try {
            $filePath = storage_path('app/public/' . str_replace('public/', '', $request->file_path));
            $gs       = $this->gsPath();

            if (!$this->isExecutableAvailable($gs)) {
                throw new \Exception('Ghostscript (gswin64c) belum terinstall atau tidak ditemukan di PATH.');
            }

            $geminiKey = env('GEMINI_OCR_API_KEY') ?: env('GEMINI_API_KEY', '');
            if (empty($geminiKey)) {
                throw new \Exception('GEMINI_API_KEY belum dikonfigurasi di file .env');
            }

            $totalPages = $this->getPdfPageCount($filePath);

            $tempDir   = storage_path('app/public/temp_ocr');
            if (!File::exists($tempDir)) File::makeDirectory($tempDir, 0755, true);

            $sessionId  = time() . '_' . rand(1000, 9999);
            $imagePaths = [];

            for ($page = 1; $page <= $totalPages; $page++) {
                $jpgPath     = $tempDir . "/doc_{$sessionId}_p{$page}.jpg";
                $tempFiles[] = $jpgPath;

                $gsCommand = "\"{$gs}\" -dNOPAUSE -dBATCH -sDEVICE=jpeg -r200 -dJPEGQ=85 "
                    . "-dFirstPage={$page} -dLastPage={$page} "
                    . "-sOutputFile=\"{$jpgPath}\" \"{$filePath}\" 2>&1";

                exec($gsCommand, $gsOut, $gsRet);

                if (File::exists($jpgPath)) {
                    $imagePaths[] = $jpgPath;
                } else {
                    \Illuminate\Support\Facades\Log::warning("Ghostscript skip halaman {$page}");
                }
            }

            if (empty($imagePaths)) {
                throw new \Exception('Tidak ada halaman yang berhasil di-render oleh Ghostscript.');
            }

            $geminiService = new GeminiOcrService();
            $rawExtractedData = $geminiService->extractFromMultipleImages($imagePaths, $totalPages);

            $extractedData = $this->handleDuplicateDocuments($rawExtractedData);

            foreach ($tempFiles as $tmp) {
                if (File::exists($tmp)) File::delete($tmp);
            }

            return response()->json([
                'status'      => 'success',
                'total_pages' => $totalPages,
                'data'        => $extractedData,
            ]);

        } catch (\Throwable $e) {
            foreach ($tempFiles as $tmp) {
                if (File::exists($tmp)) File::delete($tmp);
            }
            \Illuminate\Support\Facades\Log::error("OCR processDocument Error: " . $e->getMessage());
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    private function handleDuplicateDocuments(array $rawExtractedData): array
    {
        $baseCounts = [];
        foreach (array_keys($rawExtractedData) as $docType) {
            $baseType = trim(preg_replace('/\s*\(?\d+\)?$/', '', $docType));
            $baseCounts[$baseType] = ($baseCounts[$baseType] ?? 0) + 1;
        }

        $finalData = [];
        $docCounters = [];

        foreach ($rawExtractedData as $docType => $fields) {
            $baseType = trim(preg_replace('/\s*\(?\d+\)?$/', '', $docType));
            $docCounters[$baseType] = ($docCounters[$baseType] ?? 0) + 1;

            if ($baseCounts[$baseType] > 1) {
                $newDocType = $baseType . " (" . $docCounters[$baseType] . ")";
            } else {
                $newDocType = $baseType; 
            }
            
            $finalData[$newDocType] = $fields;
        }

        return $finalData;
    }

    //simpan
    public function saveVerifiedData(Request $request)
    {
        $request->validate([
            'permohonan_id' => 'required|integer|exists:permohonans,id',
            'ocr_data'      => 'required|array',
        ]);

        try {
            $permohonan = Permohonan::findOrFail($request->permohonan_id);
            $permohonan->update([
                'ocr_result' => $request->ocr_data,
                'ocr_status' => 'terverifikasi',
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Data OCR berhasil disimpan dan terverifikasi.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal menyimpan data: ' . $e->getMessage(),
            ], 500);
        }
    }

    //view pdf
    public function viewPdf(Request $request)
    {
        $filePath = $request->query('path');
        $fullPath = storage_path('app/public/' . str_replace('public/', '', $filePath));

        if (!\Illuminate\Support\Facades\File::exists($fullPath)) {
            abort(404, 'File tidak ditemukan di: ' . $fullPath);
        }

        return response()->file($fullPath, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($fullPath) . '"',
        ]);
    }
}