<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Participation;
use App\Models\ParticipationDetail;

class PuzzlerController extends Controller {

    public function checkWord(Request $request) {
        // Retrieve data from the session
        $randomString = session('random_string');
        $alphabetsLeft = session('alphabets_left');
        $wordsMade = session('words_made');
        $totalScore = session('total_score', 0); // Initialize total score if not set
        //Only alphabets allowed
        $word = strtoupper(preg_replace('/[^A-Za-z]/', '', $request->input('word')));

        // Validate the word against the alphabets left and other conditions
        if ($this->isValidWord($word, $alphabetsLeft) && !$this->isDuplicateWord($word, $wordsMade)) {
            // Check if the word is a real English word
            if ($this->isAnEnglishWord($word)) {
                // Update alphabets left
                $alphabetsLeft = $this->updateAlphabetsLeft($word, $alphabetsLeft);

                // Update words made
                $wordsMade[] = $word;

                // Update session variables
                session(['alphabets_left' => $alphabetsLeft]);
                session(['words_made' => $wordsMade]);

                // Calculate score based on the number of letters used in the word
                $wordScore = strlen($word);
                $totalScore += $wordScore; // Add word score to total score
                // Update total score in session
                session(['total_score' => $totalScore]);

                // Return success response with total score and word score
                return response()->json(['success' => true,
                            'message' => '<p class="text-success"> Good Job..! Move on.</p>',
                            'total_score' => $totalScore,
                            'word_score' => $wordScore,
                            'word' => $word,
                            'alphabets_left' => $alphabetsLeft]);
            } else {
                // Return error response for non-English word
                return response()->json(['success' => false, 'message' => '<p class="text-danger">Word is not a valid English word.</p>']);
            }
        } else {
            // Return error response for invalid or duplicate word
            return response()->json(['success' => false, 'message' => '<p class="text-danger">Word is invalid or duplicate.</p>']);
        }
    }

    private function isValidWord($word, $alphabetsLeft) {
        $wordArray = str_split($word);
        //dd($wordArray);
        //dd($alphabetsLeft);
        // Check if each letter in the word is in the alphabets left and within available repetitions.
        foreach ($wordArray as $letter) {
            if (($key = array_search($letter, $alphabetsLeft)) !== false) {
                // If the letter exists, remove it from the alphabets left array
                unset($alphabetsLeft[$key]);
            } else {
                return false;
            }
        }

        return true;
    }

    private function isDuplicateWord($word, $wordsMade) {
        if (is_array($wordsMade)) {
            $wordsMadeString = implode(',', $wordsMade);
        } else {
            $wordsMadeString = $wordsMade;
        }
        $wordsMadeArray = explode(',', $wordsMadeString);

        if (in_array($word, $wordsMadeArray)) {
            return true; // Word is a duplicate
        }

        return false; // Word is not a duplicate
    }

    public function isAnEnglishWord($word) {
        // Initialize a cURL session
        $apiUrl = config('constants.WORD_IDENTIFIER') . $word;
        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Ignore SSL certificate verification
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the response as a string
        $response = curl_exec($ch);
        curl_close($ch);

        $responseJson = json_decode($response, true);

        if (is_array($responseJson)) {
            if (isset($responseJson['title']) && $responseJson['title'] === "No Definitions Found") {
                return false;
            }
            if (isset($responseJson[0]) && isset($responseJson[0]['word'])) {
                $wordData = $responseJson[0];
                if (strtolower($wordData['word']) === strtolower($word)) {
                    return true; // Word is a real English word
                }
            }
        }

        return false; // Word is not a real English word.
    }

    private function updateAlphabetsLeft($word, $alphabetsLeft) {
        $wordAlphabets = str_split($word);
        $wordAlphabetCount = array_count_values($wordAlphabets);

        // Loop through each alphabet used in the word along with its count
        foreach ($wordAlphabetCount as $alphabet => $count) {
            while ($count > 0 && in_array($alphabet, $alphabetsLeft)) {
                $key = array_search($alphabet, $alphabetsLeft);
                unset($alphabetsLeft[$key]);
                $alphabetsLeft = array_values($alphabetsLeft); // Re-index the array
                $count--;
            }
        }

        return $alphabetsLeft; // Return the updated alphabetsLeft array
    }

    public function endGame(Request $request) {
        $sessionData = session()->all();
        $participationId = session('participant_id');
        $words = session('words_made');
        $score = session('total_score');

        $participationDetail = new ParticipationDetail();
        $participationDetail->participation_id = $participationId;
        $participationDetail->words_scores = json_encode($words);
        $participationDetail->save();

        // Updating score on participation table
        $participation = Participation::find($participationId);

        if ($participation) {
            $participation->score = $score;
            $participation->save();
        }
        // Clear the session data 
        Session::flush();
        return response()->json(['success' => true, 'message' => 'Game ended successfully.']);
    }
}
