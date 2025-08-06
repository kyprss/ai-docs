<?php

declare(strict_types=1);

namespace Kyprss\AiDocs;

use Kyprss\AiDocs\Commands\InitCommand;
use Kyprss\AiDocs\Commands\SyncCommand;
use Kyprss\AiDocs\Container\ContainerBuilder;
use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class Application extends SymfonyApplication
{
    private readonly ContainerInterface $container;

    public function __construct()
    {
        parent::__construct('AI Docs', '1.0.0');

        $this->container = ContainerBuilder::build();

        $this->addCommands([
            $this->container->get(InitCommand::class),
            $this->container->get(SyncCommand::class),
        ]);
    }
}
