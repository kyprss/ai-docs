<?php

declare(strict_types=1);

namespace Kyprss\AiDocs\Actions\Documentation;

use Kyprss\AiDocs\Exceptions\FileSystemException;
use Kyprss\AiDocs\Services\FileSystemService;

final class CleanupObsoleteDirectoriesAction
{
    public function __construct(
        private readonly FileSystemService $fileSystemService
    ) {}

    /**
     * @throws FileSystemException
     */
    public function execute(string $targetPath, array $configuredSources): array
    {
        $targetDir = mb_rtrim($targetPath, '/');

        if (! $this->fileSystemService->exists($targetDir)) {
            return [];
        }

        $existingDirs = $this->fileSystemService->getDirectoryContents($targetDir);
        $obsoleteDirs = array_diff($existingDirs, $configuredSources);

        foreach ($obsoleteDirs as $obsoleteDir) {
            $dirPath = $targetDir.'/'.$obsoleteDir;
            $this->fileSystemService->remove($dirPath);
        }

        return $obsoleteDirs;
    }
}
