<?php

declare(strict_types=1);

namespace Kyprss\AiDocs\Actions\Sync;

use Exception;
use Kyprss\AiDocs\Actions\Repository\CleanupTempDirectoryAction;
use Kyprss\AiDocs\Data\SourceData;
use Kyprss\AiDocs\Data\SyncResultData;
use Kyprss\AiDocs\Exceptions\SyncException;
use Kyprss\AiDocs\Services\FileSystemService;
use Symfony\Component\Process\Process;

final class SyncOpenApiAction
{
    public function __construct(
        private readonly FileSystemService $fileSystemService,
        private readonly CleanupTempDirectoryAction $cleanupTempDirectoryAction
    ) {}

    public function execute(SourceData $source, string $targetPath): SyncResultData
    {
        if (! $this->isOpenApiType($source->type)) {
            throw SyncException::unsupportedType($source->type);
        }

        $tempDir = null;

        try {
            // Create temp directory
            $tempDir = sys_get_temp_dir().'/ai-docs-'.uniqid();
            $this->fileSystemService->mkdir($tempDir);

            // Download OpenAPI JSON to temp directory
            $tempFile = $tempDir.'/openapi.json';
            $this->downloadFile($source->url, $tempFile);

            // Parse JSON and extract paths
            $openApiSpec = json_decode(file_get_contents($tempFile), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new SyncException('Failed to parse OpenAPI JSON: '.json_last_error_msg());
            }

            // Create target directory
            $destDir = mb_rtrim($targetPath, '/').'/'.$source->name;
            $this->fileSystemService->remove($destDir);
            $this->fileSystemService->mkdir($destDir);

            // Extract and save paths to individual files
            $files = [];
            if (isset($openApiSpec['paths'])) {
                foreach ($openApiSpec['paths'] as $path => $pathData) {
                    $filename = $this->pathToFilename($path);
                    $filePath = $destDir.'/'.$filename;
                    
                    // Save path data as JSON
                    $content = json_encode([
                        'path' => $path,
                        'methods' => $pathData
                    ], JSON_PRETTY_PRINT);
                    
                    $this->fileSystemService->dumpFile($filePath, $content);
                    $files[] = $filename;
                }
            }

            return SyncResultData::success($source->name, $files);

        } catch (Exception $e) {
            return SyncResultData::failure($source->name, $e->getMessage());
        } finally {
            if ($tempDir) {
                $this->cleanupTempDirectoryAction->execute($tempDir);
            }
        }
    }

    private function isOpenApiType(string $type): bool
    {
        return $type === 'openapi3';
    }

    private function downloadFile(string $url, string $destination): void
    {
        $process = new Process(['curl', '-L', '-o', $destination, $url]);
        $process->setTimeout(300); // 5 minutes timeout
        $process->run();

        if (! $process->isSuccessful()) {
            throw new SyncException('Failed to download OpenAPI file: '.$process->getErrorOutput());
        }
    }

    private function pathToFilename(string $path): string
    {
        // Convert path to safe filename
        $filename = trim($path, '/');
        $filename = str_replace('/', '_', $filename);
        $filename = preg_replace('/[^a-zA-Z0-9_\-{}]/', '_', $filename);
        $filename = preg_replace('/_+/', '_', $filename);
        
        // Add .json extension
        return $filename.'.json';
    }
}