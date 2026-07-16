<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TfIdfService;
use App\Services\GeminiGenerationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class IvaController extends Controller
{
    protected TfIdfService $tfIdfService;
    protected GeminiGenerationService $geminiService;

    public function __construct(TfIdfService $tfIdfService, GeminiGenerationService $geminiService)
    {
        $this->tfIdfService = $tfIdfService;
        $this->geminiService = $geminiService;
    }

    public function sendMessage(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'history' => 'nullable|array' 
        ]);

        $userMessage = $request->input('message');
        $history = $request->input('history', []); 

        $matchResult = $this->tfIdfService->findBestMatch($userMessage);

        if ($matchResult && $matchResult['score'] >= 0.85) {
            return response()->json([
                'status' => 'success',
                'reply' => $matchResult['intent']->konteks_jawaban, 
                'source' => 'database'
            ]);
        }

        try {
            Log::info("Tidak ditemukan di DB, langsung mencoba AI Generative...");

            $systemPrompt = "Anda adalah Intelligent Virtual Assistant (IVA) di Kantor Notaris & PPAT Lilis Aenun Jariah, S.H., M.Kn., Kabupaten Cianjur. Tugas Anda: 1. Memberikan definisi layanan akta. 2. Menyebutkan daftar syarat dokumen secara lengkap. 3. Menjelaskan alur prosedur. 4. Menginformasikan komponen biaya (tanpa menyebut nominal mutlak jika belum ada perhitungan staf). Jangan beropini di luar data ini. Jika pertanyaan di luar konteks, alihkan ke Staf.";

            $botReply = $this->geminiService->generateResponse(
                $userMessage, 
                $systemPrompt,
                $history 
            );

            return response()->json([
                'status' => 'success', 
                'reply' => $botReply,
                'source' => 'ai_generative'
            ]);

        } catch (\Exception $e) {
            Log::error("API Error: " . $e->getMessage());
            
            return response()->json([
                'status' => 'fallback', 
                'reply' => 'Maaf, sistem sedang sibuk (Limit Request). Silakan coba 1 menit lagi atau hubungi staf admin kami.'
            ]);
        }
    }

    public function index()
    {
        return view('iva.index');
    }
}