<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class GeminiGenerationService
{
    protected string $apiKey;
    protected string $apiUrl;

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY');
        $model = 'gemini-2.5-flash'; 
        
        $this->apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$this->apiKey}";
    }

    public function generateResponse(string $userQuery, string $konteksHukum): ?string
    {
        $payload = [
            'system_instruction' => [
                'parts' => [
                    ['text' => "Anda adalah Asisten Virtual (IVA) resmi untuk Kantor Notaris & PPAT. Jawab HANYA berdasarkan konteks berikut: " . $konteksHukum]
                ]
            ],
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        ['text' => $userQuery]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.0,
            ]
        ];

        try {
            $response = Http::withoutVerifying()
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($this->apiUrl, $payload);

            if ($response->successful()) {
                return $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? "Maaf, jawaban tidak dapat diproses.";
            }

            Log::error('Gemini API Error:', ['response' => $response->body()]);
            return "Maaf, sistem sedang sibuk (Error Code: " . $response->status() . ").";

        } catch (Exception $e) {
            Log::error('Gemini Connection Error: ' . $e->getMessage());
            return "Maaf, terjadi masalah koneksi ke server AI.";
        }
    }
}