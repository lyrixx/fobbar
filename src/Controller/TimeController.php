<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\Cache;

final class TimeController extends AbstractController
{
    #[Cache(public: true, maxage: 10)]
    public function index(): Response
    {
        return new Response(date('H:i:s'));
    }
}
