<?php

namespace App\Word\Checker;

use App\Word\WordCheckerInterface;

class StaticWordChecker implements WordCheckerInterface
{
    private const BANNED_WORDS = [
        'dog',
        'cat',
    ];

    public function isValid(string $word): bool
    {
        return !\in_array($word, self::BANNED_WORDS, true);
    }
}
