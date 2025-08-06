<?php

declare(strict_types=1);

namespace Kyprss\AiDocs\Actions\Sync;

use Exception;
use Kyprss\AiDocs\Actions\Documentation\CopyDocumentationFilesAction;
use Kyprss\AiDocs\Actions\Documentation\CreateReferenceFileAction;
use Kyprss\AiDocs\Actions\Repository\CleanupTempDirectoryAction;
use Kyprss\AiDocs\Actions\Repository\CloneRepositoryAction;
use Kyprss\AiDocs\Actions\Repository\FindMatchingFilesAction;
use Kyprss\AiDocs\Data\SourceData;
use Kyprss\AiDocs\Data\SyncResultData;
use Kyprss\AiDocs\Exceptions\RepositoryException;
use Kyprss\AiDocs\Services\FileSystemService;

final class SyncRepositoryAction
{
    public function __construct(
        private readonly CloneRepositoryAction $cloneRepositoryAction,
        private readonly FindMatchingFilesAction $findMatchingFilesAction,
        private readonly CopyDocumentationFilesAction $copyDocumentationFilesAction,
        private readonly CreateReferenceFileAction $createReferenceFileAction,
        private readonly CleanupTempDirectoryAction $cleanupTempDirectoryAction,
        private readonly FileSystemService $fileSystemService
    ) {}

    public function execute(SourceData $source, string $targetPath): SyncResultData
    {
        if (! $source->isRepository()) {
            throw RepositoryException::unsupportedType($source->type);
        }

        $tempDir = null;

        try {
            $tempDir = $this->cloneRepositoryAction->execute($source);

            $files = $this->findMatchingFilesAction->execute($tempDir, $source);

            $destDir = mb_rtrim($targetPath, '/').'/'.$source->name;
            $this->fileSystemService->remove($destDir);

            $this->copyDocumentationFilesAction->execute($files, $tempDir, $destDir);

            $this->createReferenceFileAction->execute($source->name, $files, $destDir, $tempDir);

            return SyncResultData::success($source->name, $files);

        } catch (Exception $e) {
            return SyncResultData::failure($source->name, $e->getMessage());
        } finally {
            if ($tempDir) {
                $this->cleanupTempDirectoryAction->execute($tempDir);
            }
        }
    }
}
