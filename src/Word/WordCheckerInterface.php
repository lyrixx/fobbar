<?php

namespace App\Word;

interface WordCheckerInterface
{
    public function isValid(string $word): bool;
}
