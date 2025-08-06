<?php

declare(strict_types=1);

namespace Kyprss\AiDocs\Actions\Config;

use Kyprss\AiDocs\Data\ConfigurationData;
use Kyprss\AiDocs\Exceptions\ConfigurationException;
use Kyprss\AiDocs\Services\ConfigurationService;

final class CreateConfigurationAction
{
    public function __construct(
        private readonly ConfigurationService $configService
    ) {}

    /**
     * @throws ConfigurationException
     */
    public function execute(): ConfigurationData
    {
        $config = new ConfigurationData(
            targetPath: '.ai/docs/',
            sources: [
                'laravel' => [
                    'type' => 'repository',
                    'url' => 'git@github.com:laravel/docs.git',
                    'branch' => '12.x',
                    'files' => ['*.md'],
                ],
            ]
        );

        $this->configService->save($config);

        return $config;
    }
}
