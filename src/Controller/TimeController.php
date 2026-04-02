<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Routing\Attribute\Route;

final class TimeController extends AbstractController
{
    #[Cache(public: true, maxage: 10)]
    #[Route('/time', name: 'app_time')]
    public function index(): Response
    {
        return new Response(date('H:i:s'));
    }
}
