<?php

declare(strict_types=1);

namespace Kyprss\AiDocs\Actions\Config;

use Kyprss\AiDocs\Data\ConfigurationData;
use Kyprss\AiDocs\Exceptions\ConfigurationException;
use Kyprss\AiDocs\Services\ConfigurationService;

final class LoadConfigurationAction
{
    public function __construct(
        private readonly ConfigurationService $configService
    ) {}

    /**
     * @throws ConfigurationException
     */
    public function execute(): ConfigurationData
    {
        return $this->configService->load();
    }

    public function configExists(): bool
    {
        return $this->configService->exists();
    }
}
