<?php

declare(strict_types=1);

namespace Kyprss\AiDocs\Exceptions;

final class ValidationException extends AiDocsException
{
    public static function invalidUrl(string $url): self
    {
        return new self("Invalid URL: {$url}");
    }

    public static function missingRequiredField(string $field): self
    {
        return new self("Missing required field: {$field}");
    }

    public static function invalidFilePattern(string $pattern): self
    {
        return new self("Invalid file pattern: {$pattern}");
    }

    public static function emptySourcesList(): self
    {
        return new self('No sources configured');
    }

    public static function invalidBranch(string $branch): self
    {
        return new self("Invalid branch name: {$branch}");
    }
}
