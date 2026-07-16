<?php

namespace App\Services;

use App\Models\PolaBakuNlu;
use Illuminate\Support\Facades\Log;

class TfIdfService
{
    protected float $threshold = 0.85;

    protected array $stopwords = [
        'yang', 'di', 'ke', 'dari', 'dan', 'atau', 'untuk', 'dengan', 
        'adalah', 'itu', 'ini', 'apa', 'bagaimana', 'saja', 'cara', 'saya', 'mau'
    ];

    /**
     * Fungsi Utama untuk mencari Intent terbaik berdasarkan input klien
     */
    public function findBestMatch(string $userQuery): ?array
    {
        $corpusData = PolaBakuNlu::with('intent')->get();
        if ($corpusData->isEmpty()) {
            return null;
        }
        $queryTokens = $this->preprocess($userQuery);
        if (empty($queryTokens)) {
            return null;
        }

        $documents = [];
        foreach ($corpusData as $data) {
            $documents[$data->id] = $this->preprocess($data->pola_pertanyaan);
        }

        $allDocs = array_merge([$queryTokens], array_values($documents));
        $idf = $this->calculateIdf($allDocs);

        $userVector = $this->calculateTfIdf($queryTokens, $idf);

        $bestScore = 0.0;
        $bestIntent = null;

        foreach ($corpusData as $data) {
            $docTokens = $documents[$data->id];
            
            if (empty($docTokens)) continue;

            $docVector = $this->calculateTfIdf($docTokens, $idf);
            $score = $this->cosineSimilarity($userVector, $docVector);

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestIntent = $data->intent;
            }
        }

        if ($bestScore >= $this->threshold && $bestIntent) {
            return [
                'intent' => $bestIntent,
                'score' => round($bestScore, 4)
            ];
        }

        Log::info("NLU Fallback: Kueri '{$userQuery}' hanya mendapat skor {$bestScore}");
        return null; 
    }

    /**
     * Text Preprocessing: Case folding, Punctuation removal, Tokenization & Stopword removal
     */
    private function preprocess(string $text): array
    {
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9\s]/', '', $text);
        $tokens = explode(' ', $text);
        
        $filteredTokens = array_filter($tokens, function($word) {
            return !in_array($word, $this->stopwords) && trim($word) !== '';
        });

        return array_values($filteredTokens);
    }

    /**
     * Menghitung Inverse Document Frequency (IDF)
     */
    private function calculateIdf(array $allDocuments): array
    {
        $idf = [];
        $totalDocs = count($allDocuments);

        foreach ($allDocuments as $docTokens) {
            $uniqueWords = array_unique($docTokens);
            foreach ($uniqueWords as $word) {
                if (!isset($idf[$word])) {
                    $idf[$word] = 0;
                }
                $idf[$word]++;
            }
        }

        foreach ($idf as $word => $count) {
            $idf[$word] = log($totalDocs / $count);
        }

        return $idf;
    }

    /**
     * Menghitung Term Frequency (TF) dikali IDF untuk membentuk Vector
     */
    private function calculateTfIdf(array $tokens, array $idf): array
    {
        $vector = [];
        $totalTokens = count($tokens);
        $termCounts = array_count_values($tokens);

        foreach ($idf as $word => $idfValue) {
            $tf = isset($termCounts[$word]) ? ($termCounts[$word] / $totalTokens) : 0;
            $vector[$word] = $tf * $idfValue;
        }

        return $vector;
    }

    /**
     * Menghitung Cosine Similarity antara dua vector (A dan B)
     */
    private function cosineSimilarity(array $vecA, array $vecB): float
    {
        $dotProduct = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        foreach ($vecA as $word => $valueA) {
            $valueB = $vecB[$word] ?? 0.0;
            
            $dotProduct += ($valueA * $valueB);
            $normA += pow($valueA, 2);
        }

        foreach ($vecB as $valueB) {
            $normB += pow($valueB, 2);
        }

        if ($normA == 0.0 || $normB == 0.0) {
            return 0.0;
        }

        return $dotProduct / (sqrt($normA) * sqrt($normB));
    }
}