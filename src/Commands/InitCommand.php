<?php

declare(strict_types=1);

namespace Kyprss\AiDocs\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class InitCommand extends Command
{
    protected static $defaultName = 'init';

    protected static $defaultDescription = 'Initialize AI Docs configuration';

    public function __construct()
    {
        parent::__construct('init');
        $this->setDescription('Initialize AI Docs configuration');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $configPath = getcwd().'/ai-docs.json';

        if (file_exists($configPath)) {
            $io->warning('Configuration file ai-docs.json already exists.');

            return Command::SUCCESS;
        }

        $config = [
            'config' => [
                'target_path' => '.ai/docs/',
            ],
            'sources' => [
                'laravel' => [
                    'type' => 'repository',
                    'url' => 'git@github.com:laravel/docs.git',
                    'branch' => '12.x',
                    'files' => ['*.md'],
                ],
            ],
        ];

        if (file_put_contents($configPath, json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) !== false) {
            $io->success('Configuration file ai-docs.json created successfully.');
        } else {
            $io->error('Failed to create configuration file.');

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
