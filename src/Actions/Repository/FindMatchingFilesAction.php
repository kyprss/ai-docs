<?php

declare(strict_types=1);

namespace Kyprss\AiDocs\Actions\Repository;

use Kyprss\AiDocs\Data\SourceData;
use Kyprss\AiDocs\Services\FileSystemService;

final class FindMatchingFilesAction
{
    public function __construct(
        private readonly FileSystemService $fileSystemService
    ) {}

    public function execute(string $directory, SourceData $source): array
    {
        return $this->fileSystemService->findMatchingFiles($directory, $source->files);
    }
}
