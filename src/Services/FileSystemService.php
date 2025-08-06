<?php

declare(strict_types=1);

namespace Kyprss\AiDocs\Services;

use DirectoryIterator;
use Kyprss\AiDocs\Exceptions\FileSystemException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

final class FileSystemService
{
    public function __construct(
        private readonly Filesystem $filesystem = new Filesystem()
    ) {}

    public function exists(string $path): bool
    {
        return $this->filesystem->exists($path);
    }

    /**
     * @throws FileSystemException
     */
    public function remove(string $path): void
    {
        try {
            $this->filesystem->remove($path);
        } catch (IOExceptionInterface $e) {
            throw FileSystemException::fileDeleteFailed($path);
        }
    }

    /**
     * @throws FileSystemException
     */
    public function mkdir(string $path): void
    {
        try {
            $this->filesystem->mkdir($path);
        } catch (IOExceptionInterface $e) {
            throw FileSystemException::directoryCreationFailed($path);
        }
    }

    /**
     * @throws FileSystemException
     */
    public function copy(string $source, string $destination): void
    {
        try {
            $this->filesystem->mkdir(dirname($destination));
            $this->filesystem->copy($source, $destination);
        } catch (IOExceptionInterface $e) {
            throw FileSystemException::fileCopyFailed($source, $destination);
        }
    }

    /**
     * @throws FileSystemException
     */
    public function dumpFile(string $path, string $content): void
    {
        try {
            $this->filesystem->dumpFile($path, $content);
        } catch (IOExceptionInterface $e) {
            throw FileSystemException::fileWriteFailed($path);
        }
    }

    public function findMatchingFiles(string $dir, array $patterns): array
    {
        $files = [];

        if (! is_dir($dir)) {
            return $files;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        $hasDocsDir = is_dir($dir.'/docs');

        foreach ($iterator as $file) {
            $filePath = $file->getPathname();
            $fileName = $file->getFilename();
            $relativePath = str_replace($dir.'/', '', $filePath);

            $pathParts = explode('/', $relativePath);

            if (str_starts_with($pathParts[0], '.') || ($hasDocsDir && count($pathParts) === 1)) {
                continue;
            }

            foreach ($patterns as $pattern) {
                if (fnmatch($pattern, $fileName)) {
                    $files[] = $filePath;
                    break;
                }
            }
        }

        return array_unique($files);
    }

    public function getDirectoryContents(string $path): array
    {
        if (! $this->exists($path)) {
            return [];
        }

        $contents = [];
        $iterator = new DirectoryIterator($path);

        foreach ($iterator as $fileInfo) {
            if ($fileInfo->isDot() || ! $fileInfo->isDir()) {
                continue;
            }
            $contents[] = $fileInfo->getFilename();
        }

        return $contents;
    }
}
