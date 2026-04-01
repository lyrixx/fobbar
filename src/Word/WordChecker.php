<?php

namespace App\Word;

class WordChecker
{
    /** @param iterable<WordCheckerInterface> $checkers */
    public function __construct(
        private readonly iterable $checkers,
    ) {
    }

    public function getInvalidWords(string $text): array
    {
        preg_match_all('/\w+/', $text, $matches);

        $words = $matches[0];

        $invalidWords = [];

        foreach ($words as $word) {
            $word = mb_strtolower($word);
            foreach ($this->checkers as $checker) {
                if (!$checker->isValid($word)) {
                    $invalidWords[] = $word;

                    break;
                }
            }
        }

        return $invalidWords;
    }
}
