<?php

declare(strict_types=1);

namespace Kyprss\AiDocs\Exceptions;

final class FileSystemException extends AiDocsException
{
    public static function directoryNotFound(string $path): self
    {
        return new self("Directory not found: {$path}");
    }

    public static function directoryCreationFailed(string $path): self
    {
        return new self("Failed to create directory: {$path}");
    }

    public static function fileReadFailed(string $path): self
    {
        return new self("Failed to read file: {$path}");
    }

    public static function fileWriteFailed(string $path): self
    {
        return new self("Failed to write file: {$path}");
    }

    public static function fileCopyFailed(string $source, string $destination): self
    {
        return new self("Failed to copy file from {$source} to {$destination}");
    }

    public static function fileDeleteFailed(string $path): self
    {
        return new self("Failed to delete file: {$path}");
    }

    public static function permissionDenied(string $path): self
    {
        return new self("Permission denied: {$path}");
    }
}
