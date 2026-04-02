<?php

namespace App\EventListener;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;

final class MaintenanceListener
{
    public function __construct(
        #[Autowire('%kernel.project_dir%/maintenance')]
        private readonly string $maintenanceFilePath,
        private readonly Filesystem $filesystem,
    ) {
    }

    #[AsEventListener(priority: 1024)]
    public function onRequestEvent(RequestEvent $event): void
    {
        if ($this->filesystem->exists($this->maintenanceFilePath)) {
            $event->setResponse(new Response('Maintenance mode', 503));
        }
    }
}
