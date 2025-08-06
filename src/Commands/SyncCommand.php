<?php

declare(strict_types=1);

namespace Kyprss\AiDocs\Commands;

use Kyprss\AiDocs\Actions\Config\LoadConfigurationAction;
use Kyprss\AiDocs\Actions\Config\ValidateConfigurationAction;
use Kyprss\AiDocs\Actions\Sync\SyncAllSourcesAction;
use Kyprss\AiDocs\Exceptions\AiDocsException;
use Kyprss\AiDocs\Exceptions\ConfigurationException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class SyncCommand extends Command
{
    protected static $defaultName = 'sync';

    protected static $defaultDescription = 'Sync documentation from configured sources';

    public function __construct(
        private readonly LoadConfigurationAction $loadConfigAction,
        private readonly ValidateConfigurationAction $validateConfigAction,
        private readonly SyncAllSourcesAction $syncAllSourcesAction
    ) {
        parent::__construct('sync');
        $this->setDescription('Sync documentation from configured sources');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $config = $this->loadConfigAction->execute();
            $this->validateConfigAction->execute($config);

            $results = $this->syncAllSourcesAction->execute($config);

            foreach ($results as $result) {
                $io->section("Syncing: {$result->name}");

                if ($result->success) {
                    $io->text("Synced {$result->name}: {$result->getFileCount()} files");
                } else {
                    $io->warning("Failed to sync {$result->name}: {$result->error}");
                }
            }

            $io->success('All sources synced successfully.');

            return Command::SUCCESS;

        } catch (ConfigurationException $e) {
            if (str_contains($e->getMessage(), 'not found')) {
                $io->error('Configuration file ai-docs.json not found. Run "ai-docs init" first.');
            } else {
                $io->error($e->getMessage());
            }

            return Command::FAILURE;
        } catch (AiDocsException $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }
    }
}
