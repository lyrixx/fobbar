<?php

namespace App\Command;

use rfreebern\Giphy;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

#[AsCommand(
    name: 'app:gif-downloader',
    description: 'Download some cool GIF',
)]
class GifDownloaderCommand extends Command
{
    public function __construct(
        #[Autowire('%kernel.project_dir%')]
        private readonly string $projectDir,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('what', InputArgument::REQUIRED, 'What do you want to download?')
            ->addOption('where', null, InputOption::VALUE_REQUIRED, 'Where?', $this->projectDir . '/var/gifs')
            ->addOption('count', null, InputOption::VALUE_REQUIRED, 'How many?', 10)
        ;
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        if (!$input->getArgument('what')) {
            $io = new SymfonyStyle($input, $output);
            $what = $io->ask('What do you want to download?', validator: static function ($value) {
                if (!$value) {
                    throw new \InvalidArgumentException('You must provide a value');
                }

                return $value;
            });
            $input->setArgument('what', $what);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $what = $input->getArgument('what');
        $count = $input->getOption('count');
        $where = $input->getOption('where');

        $giphy = new Giphy('ifG2C6hnDx9iAenP3Rgeg8fSV4Wifdiw');
        $data = $giphy->search($what, $count)->data;

        if (!$data) {
            $io->error('No GIF found');

            return Command::FAILURE;
        }

        $fs = new Filesystem();
        $gifs = [];

        $io->progressStart(is_countable($data) ? \count($data) : 0);

        foreach ($data as $gif) {
            $file = $where . '/' . basename($gif->url) . '.gif';

            $fs->copy($gif->images->original->url, $file);

            $gifs[] = [
                $gif->title,
                Path::makeRelative($file, getcwd()),
            ];

            $io->progressAdvance();
        }

        $io->progressFinish();

        $io->table(['Title', 'File'], $gifs);

        return Command::SUCCESS;
    }
}
