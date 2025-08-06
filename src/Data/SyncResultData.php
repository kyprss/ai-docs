<?php

declare(strict_types=1);

namespace Kyprss\AiDocs\Data;

final readonly class SyncResultData
{
    public function __construct(
        public string $name,
        public array $files,
        public bool $success,
        public ?string $error = null
    ) {}

    public static function success(string $name, array $files): self
    {
        return new self($name, $files, true);
    }

    public static function failure(string $name, string $error): self
    {
        return new self($name, [], false, $error);
    }

    public function getFileCount(): int
    {
        return count($this->files);
    }
}
