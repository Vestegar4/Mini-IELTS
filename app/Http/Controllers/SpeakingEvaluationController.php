<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Attempt;
use Illuminate\Http\Request;
use App\Services\GeminiEvaluationService;

class SpeakingEvaluationController extends Controller
{
    public function getQuestion()
    {
        return response()->json(Question::all());
    }

    public function submitAttempt(Request $request, GeminiEvaluationService $gemini)
    {
        $request->validate([
            'question_id' => 'required|exists:questions,id',
            'answer' => 'required|string|min:5',
        ]);

        $question = Question::findOrFail($request->input('question_id'));
        $evaluation = $gemini->evaluate($question->prompt, $request->answer);

        $attempt = Question::findOrFail($request->question_id);

        $evaluation = $gemini->evaluate($question->prompt, $request->answer);

        $attempt = Attempt::create([
            'question_id' => $question->id,
            'answer' => $request->answer,
            'band_score' => $evaluation['band_score'],
            'strengths' => $evaluation['strengths'],
            'improvements' => $evaluation['improvements'],
        ]);
        return response()->json([
            'message' => 'Answer submitted and evaluated successfully.',
            'data' => $attempt->load('question')
        ]);
    }
    public function getAttempts()
    {
        return response()->json(Attempt::with('question')->latest()->get());
    }
}