<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

class SpeakingEvaluationTest extends TestCase
{
    use RefreshDatabase; // Mengembalikan kondisi database ke awal setiap kali test selesai

    public function test_user_can_submit_answer_and_get_evaluation()
    {
        // 1. Persiapan Data (Setup)
        $question = Question::create([
            'part' => 1,
            'topic' => 'Hobbies',
            'prompt' => 'What is your hobby?'
        ]);

        // 2. Mocking HTTP Request ke API Gemini
        // Kita mencegat request keluar ke googleapis.com dan memalsukan balasan sukses (200 OK)
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => json_encode([
                                    'band_score' => 7.5,
                                    'strengths' => 'Vocabulary is diverse and appropriate.',
                                    'improvements' => 'Work on sentence fluency.'
                                ])]
                            ]
                        ]
                    ]
                ]
            ], 200)
        ]);

        // 3. Eksekusi (Action) menembak API milik kita sendiri
        $response = $this->postJson('/api/speaking/submit', [
            'question_id' => $question->id,
            'answer' => 'I really love reading science fiction books in my free time.'
        ]);

        // 4. Validasi (Assertion)
        $response->assertStatus(200)
                 ->assertJsonPath('data.band_score', 7.5);

        // Pastikan data tersimpan di database
        $this->assertDatabaseHas('attempts', [
            'question_id' => $question->id,
            'band_score' => 7.5,
        ]);
    }
}