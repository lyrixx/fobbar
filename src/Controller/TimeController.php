<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

final class TimeController extends AbstractController
{
    public function __construct(
        private CacheInterface $cache,
    ) {
    }

    #[Cache(public: true, maxage: 5)]
    public function index(): Response
    {
        $time = $this->cache->get('current_time_', $this->getTime(...));

        return new Response($time);
    }

    private function getTime(ItemInterface $item): string
    {
        $item->expiresAfter(10);

        return date('H:i:s');
    }
}
