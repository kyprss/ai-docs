<?php

declare(strict_types=1);

namespace Kyprss\AiDocs\Exceptions;

final class ConfigurationException extends AiDocsException
{
    public static function fileNotFound(string $path): self
    {
        return new self("Configuration file not found: {$path}");
    }

    public static function invalidFormat(): self
    {
        return new self('Invalid configuration file format');
    }

    public static function creationFailed(string $path): self
    {
        return new self("Failed to create configuration file: {$path}");
    }

    public static function parseFailed(string $path, string $error): self
    {
        return new self("Failed to parse configuration file {$path}: {$error}");
    }

    public static function invalidJson(string $error): self
    {
        return new self("Invalid JSON in configuration file: {$error}");
    }

    public static function missingConfig(string $key): self
    {
        return new self("Missing configuration key: {$key}");
    }
}
