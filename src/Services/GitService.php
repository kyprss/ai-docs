<?php

declare(strict_types=1);

namespace Kyprss\AiDocs\Services;

use Kyprss\AiDocs\Data\SourceData;
use Kyprss\AiDocs\Exceptions\RepositoryException;
use Symfony\Component\Process\Process;

final class GitService
{
    /**
     * @throws RepositoryException
     */
    public function clone(SourceData $source, string $targetDir): void
    {
        $command = ['git', 'clone', '--depth', '1'];

        if ($source->branch) {
            $command[] = '--branch';
            $command[] = $source->branch;
        }

        $command[] = $source->url;
        $command[] = $targetDir;

        $process = new Process($command);
        $process->run();

        if (! $process->isSuccessful()) {
            throw RepositoryException::cloneFailed($source->url, $process->getErrorOutput());
        }
    }
}
