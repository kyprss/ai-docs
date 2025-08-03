<?php

declare(strict_types=1);

namespace Kyprss\AiDocs;

use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Filesystem\Filesystem;
use Kyprss\AiDocs\Commands\InitCommand;
use Kyprss\AiDocs\Commands\SyncCommand;

class Application extends SymfonyApplication
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