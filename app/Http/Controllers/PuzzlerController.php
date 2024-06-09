<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Arr;
use App\Models\Participation;
use App\Models\ParticipationDetail;

class PuzzlerController extends Controller {

    private $usedLetters = []; // Track used letters by the student

    public function checkWord(Request $request) 
    {
        // Retrieve data from the session
        $randomString = session('random_string');
        $alphabetsLeft = session('alphabets_left');
        $wordsMade = session('words_made');
        $totalScore = session('total_score', 0); // Initialize total score if not set
        // Only alphabets allowed
        $word = strtoupper(preg_replace('/[^A-Za-z]/', '', $request->input('word')));

        // Validate the word against the alphabets left and other conditions
        if ($this->isValidWord($word, $alphabetsLeft) && !$this->isDuplicateWord($word, $wordsMade)) {
            // Check if the word is a real English word
            if ($this->isAnEnglishWord($word)) {

                $alphabetsLeft = $this->updateAlphabetsLeft($word, $alphabetsLeft);
                $wordsMade[] = ['word' => $word, 'score' => strlen($word)];
                $this->updateUsedLetters($word);
                session(['alphabets_left' => $alphabetsLeft]);
                session(['words_made' => $wordsMade]);

                // Calculate score based on the number of letters used in the word
                $wordScore = strlen($word);
                $totalScore += $wordScore; // Add word score to total score
                session(['total_score' => $totalScore]);

                // Return success response with total score and word score
                return response()->json([
                            'success' => true,
                            'message' => '<p class="text-success">Congratzz! Move on.</p>',
                            'total_score' => $totalScore,
                            'word_score' => $wordScore,
                            'word' => $word,
                            'alphabets_left' => $alphabetsLeft
                ]);
            } else {
                return response()->json(['success' => false, 'message' => '<p class="text-danger">Word is not a valid English word.</p>']);
            }
        } else {
            // Return error response for invalid or duplicate word
            return response()->json(['success' => false, 'message' => '<p class="text-danger">Word is invalid or duplicate.</p>']);
        }
    }

    private function updateUsedLetters($word) 
    {
        $wordLetters = str_split($word);
        $this->usedLetters = array_unique(array_merge($this->usedLetters, $wordLetters));
    }

    private function isValidWord($word, $alphabetsLeft) 
    {
        $wordArray = str_split($word);
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

    private function isDuplicateWord($word, $wordsMade) 
    {
        foreach ($wordsMade as $wordData) {
            if ($wordData['word'] === $word) {
                return true; // Word is a duplicate
            }
        }
        return false; // Word is not a duplicate
    }

    public function isAnEnglishWord($word) 
    {
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

    private function updateAlphabetsLeft($word, $alphabetsLeft) 
    {
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

    public function endGame(Request $request) 
    {
//        $sessions = session()->all();
//        dd($sessions);
        $participationId = session('participant_id');
        $wordsWithScores = session('words_made');
        $score = session('total_score');

        $participationDetail = new ParticipationDetail();
        $participationDetail->participation_id = $participationId;
        $participationDetail->words_scores = json_encode($wordsWithScores);
        $participationDetail->save();

        // Updating score on participation table
        $participation = Participation::find($participationId);

        if ($participation) {
            $participation->score = $score;
            $participation->save();
        }

        // Clear the session data
        Session::flush();

        return response()->json([
            'success' => true,
            'status' => 'GameEnded',
            'message' => 'Game ended successfully.',
            'score' => $score,
            'words' => json_encode($wordsWithScores),
        ]);
    }
    
    public function getHighScores() 
    {
        // Fetch the top ten highest-scoring submissions without duplicate words
        $topScores = Participation::select('id', 'participant_id', 'score')
            ->with(['details' => function ($query) {
                $query->select('participation_id', 'words_scores')->orderByDesc('id');
            }])
            ->orderByDesc('score')
            ->take(10)
            ->get();

        // Filter duplicate words in the top scores
        $filteredScores = [];
        foreach ($topScores as $score) {
            $uniqueWords = [];
            $filteredWordsScores = [];
            foreach ($score->details as $detail) {
                $wordsScores = json_decode($detail->words_scores, true);
                foreach ($wordsScores as $wordScore) {
                    if (!in_array($wordScore['word'], $uniqueWords)) {
                        $uniqueWords[] = $wordScore['word'];
                        $filteredWordsScores[] = $wordScore;
                    }
                }
            }
            if (!empty($filteredWordsScores)) {
                $score->filteredWordsScores = $filteredWordsScores;
                $filteredScores[] = $score;
            }
        }

        return view('high_scores', ['topScores' => $filteredScores]);
    }


}
