<?php

declare(strict_types=1);

namespace Kyprss\AiDocs\Data;

final readonly class ConfigurationData
{
    public function __construct(
        public string $targetPath,
        public array $sources
    ) {}

    public static function fromArray(array $config): self
    {
        return new self(
            targetPath: $config['config']['target_path'] ?? '.ai/docs/',
            sources: $config['sources'] ?? []
        );
    }

    public function toArray(): array
    {
        return [
            'config' => [
                'target_path' => $this->targetPath,
            ],
            'sources' => $this->sources,
        ];
    }
}
