<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Question;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Question::create([
            'part' => '1',
            'topic' => 'Work or Studies',
            'prompt' => 'Do you work or are you a student?',
        ]);
        Question::create([
            'part' => '1',
            'topic' => 'Hobbies',
            'prompt' => 'What do you like to do in your free time?',
        ]);
        Question::create([
            'part' => '2',
            'topic' => 'Describe a place you have visited',
            'prompt' => 'Tell me about a place you have visited.',
        ]);
    }
}
