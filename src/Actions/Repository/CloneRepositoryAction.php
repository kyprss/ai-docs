<?php

declare(strict_types=1);

namespace Kyprss\AiDocs\Actions\Repository;

use Kyprss\AiDocs\Data\SourceData;
use Kyprss\AiDocs\Exceptions\RepositoryException;
use Kyprss\AiDocs\Services\GitService;

final class CloneRepositoryAction
{
    public function __construct(
        private readonly GitService $gitService
    ) {}

    /**
     * @throws RepositoryException
     */
    public function execute(SourceData $source): string
    {
        $tempDir = sys_get_temp_dir().'/ai-docs-'.$source->name.'-'.uniqid();

        $this->gitService->clone($source, $tempDir);

        return $tempDir;
    }
}
