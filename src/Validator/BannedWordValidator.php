<?php

namespace App\Validator;

use App\Word\Checker\BannedWordListChecker;
use App\Word\Checker\StaticWordChecker;
use App\Word\WordChecker;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class BannedWordValidator extends ConstraintValidator
{
    /** @param BannedWord $constraint */
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (null === $value || '' === $value) {
            return;
        }

        $checker = new WordChecker([
            new StaticWordChecker(),
            new BannedWordListChecker(),
        ]);

        $invalidWords = $checker->getInvalidWords($value);

        foreach ($invalidWords as $invalidWord) {
            $this->context
                ->buildViolation('The message could not contain the word "{{ value }}".')
                ->setParameter('{{ value }}', $invalidWord)
                ->addViolation()
            ;
        }
    }
}
