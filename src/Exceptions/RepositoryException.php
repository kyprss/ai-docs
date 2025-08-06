<?php

declare(strict_types=1);

namespace Kyprss\AiDocs\Exceptions;

final class RepositoryException extends AiDocsException
{
    public static function cloneFailed(string $url, string $error): self
    {
        return new self("Failed to clone repository {$url}: {$error}");
    }

    public static function unsupportedType(string $type): self
    {
        return new self("Unsupported repository type: {$type}");
    }

    public static function branchNotFound(string $branch, string $url): self
    {
        return new self("Branch '{$branch}' not found in repository: {$url}");
    }

    public static function accessDenied(string $url): self
    {
        return new self("Access denied to repository: {$url}");
    }

    public static function networkError(string $url, string $error): self
    {
        return new self("Network error accessing repository {$url}: {$error}");
    }

    public static function gitCommandFailed(string $command, string $error): self
    {
        return new self("Git command failed '{$command}': {$error}");
    }
}
