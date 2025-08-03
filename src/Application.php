<?php

declare(strict_types=1);

namespace Kyprss\AiDocs;

use Kyprss\AiDocs\Commands\InitCommand;
use Kyprss\AiDocs\Commands\SyncCommand;
use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Filesystem\Filesystem;

final class Application extends SymfonyApplication
{
    public function __construct()
    {
        parent::__construct('AI Docs', '1.0.0');

        $this->addCommands([
            new InitCommand(),
            new SyncCommand(new Filesystem()),
        ]);
    }
}
