<?php

namespace App\Word\Checker;

use App\Word\WordCheckerInterface;

class BannedWordListChecker implements WordCheckerInterface
{
    public function isValid(string $word): bool
    {
        $response = file_get_contents('http://www.bannedwordlist.com/lists/swearWords.txt');
        $words = explode("\n", $response);
        $words = array_map(trim(...), $words);

        return !\in_array($word, $words, true);
    }
}
