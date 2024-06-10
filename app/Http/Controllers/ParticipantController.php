<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Models\Participant;
use App\Models\RandomString;
use App\Models\Participation;
use App\Models\ParticipationDetail;


class ParticipantController extends Controller {

    public function store(Request $request) 
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
        ]);

        $participant = Participant::firstOrCreate(
                        ['email' => $request->email],
                        ['name' => $request->name]
        );

        // Randomly select a string from random_strings table
        $randomString = RandomString::inRandomOrder()->first();

        if (!$randomString) {
            return back()->withError('No random string available.');
        }

        $participation = Participation::create([
            'participant_id' => $participant->id,
            'string_id' => $randomString->id,
        ]);

        if (!$participation) {
            return back()->withError('Failed to register your participation.');
        }

        /* SESSION DETAILS */
        session([
            'participation_id' => $participation->id,
            'total_score' => 0,
            'random_string' => $randomString->string,
            'random_string_id' => $randomString->id,
            'participant_id' => $participant->id,
            'alphabets_left' => str_split($randomString->string), // split the string into individual alphabets
            'words_made' => [],
        ]);
        /* SESSION ENDS */
        return view('puzzler', compact('participant', 'randomString'));
    }
    
    public function showTopScores() 
    {
        $topers = Participation::with(['detail', 'participant'])
                        ->orderByDesc('score')
                        ->take(10)
                        ->get();
        //dd($topers);
        $topScoringWords = $this->topScoringWords();
        return view('topers', compact('topers', 'topScoringWords'));
    }
    
    public function topScoringWords()
    {
        // Retrieve all words_scores data from participation_details table
        $participationDetails = ParticipationDetail::all();

        // Initialize an empty array to store words and their scores
        $allWords = [];

        // Parse JSON data and calculate total score for each word
        foreach ($participationDetails as $detail) {
            $words = json_decode($detail->words_scores, true);
            foreach ($words as $word) {
                if (!isset($allWords[$word['word']])) {
                    $allWords[$word['word']] = $word['score'];
                } else {
                    $allWords[$word['word']] += $word['score'];
                }
            }
        }

        // Sort words based on total score in descending order
        arsort($allWords);

        // Select top 10 words with highest total score
        return $topWords = array_slice($allWords, 0, 10, true);

        //return view('top_words', compact('topWords'));
    }
    

}
