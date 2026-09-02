<?php

use Illuminate\Support\Facades\Route;
use App\Services\GeminiEvaluationService;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-gemini', function (GeminiEvaluationService $gemini) {
    $prompt = "Do you work or are you a student?";
    $answer = "I am currently a student studying computer science at a university.";
    $result = $gemini->evaluate($prompt, $answer);
    dd($result);
});