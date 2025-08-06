<?php

declare(strict_types=1);

namespace Kyprss\AiDocs\Services;

use Kyprss\AiDocs\Data\ConfigurationData;
use Kyprss\AiDocs\Exceptions\ConfigurationException;

final class ConfigurationService
{
    public function __construct(
        private readonly string $configPath = 'ai-docs.json'
    ) {}

    public function getConfigPath(): string
    {
        return getcwd().'/'.$this->configPath;
    }

    public function exists(): bool
    {
        return file_exists($this->getConfigPath());
    }

    /**
     * @throws ConfigurationException
     */
    public function load(): ConfigurationData
    {
        $path = $this->getConfigPath();

        if (! file_exists($path)) {
            throw ConfigurationException::fileNotFound($path);
        }

        $content = file_get_contents($path);

        if ($content === false) {
            throw ConfigurationException::parseFailed($path, 'Could not read file');
        }

        $config = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw ConfigurationException::invalidJson(json_last_error_msg());
        }

        if (! is_array($config)) {
            throw ConfigurationException::invalidFormat();
        }

        return ConfigurationData::fromArray($config);
    }

    /**
     * @throws ConfigurationException
     */
    public function save(ConfigurationData $config): void
    {
        $path = $this->getConfigPath();
        $content = json_encode($config->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        if (file_put_contents($path, $content) === false) {
            throw ConfigurationException::creationFailed($path);
        }
    }
}
