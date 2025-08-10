<?php

declare(strict_types=1);

namespace Kyprss\AiDocs\Exceptions;

final class SyncException extends AiDocsException
{
    public static function sourceFailed(string $name, string $error): self
    {
        return new self("Failed to sync source '{$name}': {$error}");
    }

    public static function fileOperationFailed(string $operation, string $path): self
    {
        return new self("File {$operation} failed for: {$path}");
    }

    public static function noMatchingFiles(string $pattern): self
    {
        return new self("No files found matching pattern: {$pattern}");
    }

    public static function referenceFileCreationFailed(string $path): self
    {
        return new self("Failed to create reference file: {$path}");
    }

    public static function targetDirectoryCreationFailed(string $path): self
    {
        return new self("Failed to create target directory: {$path}");
    }

    public static function tempDirectoryCleanupFailed(string $path): self
    {
        return new self("Failed to cleanup temporary directory: {$path}");
    }

    public static function unsupportedType(string $type): self
    {
        return new self("Unsupported source type: {$type}");
    }
}
