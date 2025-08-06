<?php

declare(strict_types=1);

namespace Kyprss\AiDocs\Data;

final readonly class SourceData
{
    public function __construct(
        public string $name,
        public string $type,
        public string $url,
        public ?string $branch = null,
        public array $files = ['*.md']
    ) {}

    public static function fromConfig(string $name, array $config): self
    {
        return new self(
            name: $name,
            type: $config['type'],
            url: $config['url'],
            branch: $config['branch'] ?? null,
            files: $config['files'] ?? ['*.md']
        );
    }

    public function isRepository(): bool
    {
        return $this->type === 'repository';
    }
}
