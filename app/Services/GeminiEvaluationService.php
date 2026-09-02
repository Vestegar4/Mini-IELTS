<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;

class GeminiEvaluationService
{
    public function evaluate(string $prompt, string$answer): array
    {
        $apiKey = trim(env('GEMINI_API'));
        if(empty($apiKey)) {
            throw new Exception('Gemini API key is not set');
        }
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-3.5-flash:generateContent?key=" . $apiKey;

        $systemInstruction = "You are an IELTS examiner. Evaluate the user's text answer to the prompt. Return ONLY a valid JSON object with exactly three keys: 'band_score' (number/float), 'strengths' (string), and 'improvements' (string).";

        $response = Http::timeout(60)->withOptions([
            'force_ip_resolve' => 'v4',
            'verify' => false,

        ])->post($url, [
            'contents' => [
                ['role' => 'user', 'parts' => [['text' => "Prompt: {$prompt}\nAnswer: {$answer}"]]]
            ],
            'systemInstruction' => [
                'parts' => [['text' => $systemInstruction]]
            ],
            'generationConfig' => [
                'responseMimeType' => 'application/json',
            ]
        ]);

        if($response->successful()) {
            $responseText =$response->json('candidates.0.content.parts.0.text');

            $responseText = preg_replace('/```(?:json)?|```/', '', $responseText);
            $responseText = trim($responseText);

            $result = json_decode($responseText, true);

            if (!is_array($result)) {
                throw new Exception('Gagal melakukan decode JSON dari response: ' . $responseText);
            }

            return [
                'band_score' => $result['band_score'] ?? 0,
                'strengths' => $result['strengths'] ?? 'No strengths identified.',
                'improvements' => $result['improvements'] ?? 'No improvements identified.',
            ];
        }
        if ($response->status() === 500 || $response->serverError()) {
            return [
                'band_score' => 6.5,
                'strengths' => '[Mocked - API Overloaded] The answer is relevant to the topic. You provided a direct and clear response.',
                'improvements' => '[Mocked - API Overloaded] Try to expand your answer by adding more specific details or personal examples.',
            ];
        }
        throw new Exception('Invalid response format from Gemini API:' . $response->body());
    }
}