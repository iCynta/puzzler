<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use App\Models\RandomString;
use App\Models\Participation;
use Illuminate\Http\Request;

class ParticipantController extends Controller {

    public function store(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
        ]);

        // Check if the participant already exists
        $participant = Participant::firstOrCreate(
                        ['email' => $request->email],
                        ['name' => $request->name]
        );

        // Randomly select a string from random_strings table
        $randomString = RandomString::inRandomOrder()->first();

        Participation::create([
            'participant_id' => $participant->id,
            'string_id' => $randomString->id,
        ]);

        /* SESSION DETAILS */
        session([
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
}
