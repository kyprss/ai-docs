<?php

declare(strict_types=1);

namespace Kyprss\AiDocs\Actions\Repository;

use Kyprss\AiDocs\Exceptions\FileSystemException;
use Kyprss\AiDocs\Services\FileSystemService;

final class CleanupTempDirectoryAction
{
    public function __construct(
        private readonly FileSystemService $fileSystemService
    ) {}

    /**
     * @throws FileSystemException
     */
    public function execute(string $tempDir): void
    {
        if ($this->fileSystemService->exists($tempDir)) {
            $this->fileSystemService->remove($tempDir);
        }
    }
}
