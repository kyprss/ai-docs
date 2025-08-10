<?php

declare(strict_types=1);

namespace Kyprss\AiDocs\Actions\Documentation;

use Kyprss\AiDocs\Exceptions\FileSystemException;
use Kyprss\AiDocs\Services\FileSystemService;

final class CopyDocumentationFilesAction
{
    public function __construct(
        private readonly FileSystemService $fileSystemService
    ) {}

    /**
     * @throws FileSystemException
     */
    public function execute(array $files, string $sourceDir, string $destDir): void
    {
        $this->fileSystemService->mkdir($destDir);

        foreach ($files as $file) {
            $relativePath = str_replace($sourceDir.'/', '', $file);
            $destFile = $destDir.'/'.$relativePath;

            $this->fileSystemService->copy($file, $destFile);
        }
    }
}
