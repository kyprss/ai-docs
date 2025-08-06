<?php

declare(strict_types=1);

namespace Kyprss\AiDocs\Actions\Config;

use Kyprss\AiDocs\Data\ConfigurationData;
use Kyprss\AiDocs\Exceptions\ConfigurationException;

final class ValidateConfigurationAction
{
    /**
     * @throws ConfigurationException
     */
    public function execute(ConfigurationData $config): void
    {
        if (empty($config->targetPath)) {
            throw ConfigurationException::invalidFormat();
        }

        if (empty($config->sources)) {
            throw ConfigurationException::invalidFormat();
        }

        foreach ($config->sources as $name => $source) {
            if (empty($source['type']) || empty($source['url'])) {
                throw ConfigurationException::invalidFormat();
            }
        }
    }
}
