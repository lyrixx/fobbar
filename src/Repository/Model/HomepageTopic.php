<?php

namespace App\Repository\Model;

use App\Entity\Topic;

class HomepageTopic
{
    public function __construct(
        public Topic $topic,
        public int $messageCount,
    ) {
    }
}
