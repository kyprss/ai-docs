<?php

declare(strict_types=1);

namespace Kyprss\AiDocs\Commands;

use Kyprss\AiDocs\Actions\Config\CreateConfigurationAction;
use Kyprss\AiDocs\Actions\Config\LoadConfigurationAction;
use Kyprss\AiDocs\Exceptions\AiDocsException;
use Kyprss\AiDocs\Exceptions\ConfigurationException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class InitCommand extends Command
{
    protected static $defaultName = 'init';

    protected static $defaultDescription = 'Initialize AI Docs configuration';

    public function __construct(
        private readonly LoadConfigurationAction $loadConfigAction,
        private readonly CreateConfigurationAction $createConfigAction
    ) {
        parent::__construct('init');
        $this->setDescription('Initialize AI Docs configuration');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if ($this->loadConfigAction->configExists()) {
            $io->warning('Configuration file ai-docs.json already exists.');

            return Command::SUCCESS;
        }

        try {
            $this->createConfigAction->execute();
            $io->success('Configuration file ai-docs.json created successfully.');

            return Command::SUCCESS;
        } catch (ConfigurationException $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        } catch (AiDocsException $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }
    }
}
