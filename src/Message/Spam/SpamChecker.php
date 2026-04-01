<?php

namespace App\Message\Spam;

use App\Entity\Message;
use App\Message\Spam\Model\SpamScore;

class SpamChecker
{
    public function getSpamScore(Message $message): SpamScore
    {
        $rand = random_int(0, 100);

        if ($rand <= 25) {
            return SpamScore::SPAM;
        }

        if ($rand <= 50) {
            return SpamScore::MAYBE;
        }

        return SpamScore::NO_SPAM;
    }
}
