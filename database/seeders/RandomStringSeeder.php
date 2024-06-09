<?php

namespace database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RandomStringSeeder extends Seeder
{
    public function run()
    {
        $randomStrings = [];
        $numberOfStrings = 10;

        for ($i = 0; $i < $numberOfStrings; $i++) {
            $randomString = $this->generateUniqueRandomAlphabetString(50, $randomStrings); // Generate a unique random string with alphabets
            $randomStrings[] = [
                'string' => $randomString,
            ];
        }

        // Insert the random strings into the database
        DB::table('random_strings')->insert($randomStrings);
    }

    private function generateUniqueRandomAlphabetString($length, $existingStrings)
    {
        do {
            $randomString = $this->generateRandomAlphabetString($length);
        } while (in_array($randomString, $existingStrings));

        return $randomString;
    }

    private function generateRandomAlphabetString($length)
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $randomString;
    }
}

