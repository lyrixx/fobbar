<?php

namespace App\Message\Spam\Model;

enum SpamScore
{
    case SPAM;
    case MAYBE;
    case NO_SPAM;
}
