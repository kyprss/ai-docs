<?php

declare(strict_types=1);

namespace Kyprss\AiDocs\Actions\Sync;

use Kyprss\AiDocs\Actions\Documentation\CleanupObsoleteDirectoriesAction;
use Kyprss\AiDocs\Data\ConfigurationData;
use Kyprss\AiDocs\Data\SourceData;
use Kyprss\AiDocs\Data\SyncResultData;
use Kyprss\AiDocs\Exceptions\FileSystemException;

final class SyncAllSourcesAction
{
    public function __construct(
        private readonly SyncRepositoryAction $syncRepositoryAction,
        private readonly SyncOpenApiAction $syncOpenApiAction,
        private readonly CleanupObsoleteDirectoriesAction $cleanupObsoleteDirectoriesAction
    ) {}

    /**
     * @throws FileSystemException
     */
    public function execute(ConfigurationData $config): array
    {
        $results = [];

        $configuredSources = array_keys($config->sources);
        $this->cleanupObsoleteDirectoriesAction->execute($config->targetPath, $configuredSources);

        foreach ($config->sources as $name => $sourceConfig) {
            $source = SourceData::fromConfig($name, $sourceConfig);
            
            if ($source->isRepository()) {
                $results[] = $this->syncRepositoryAction->execute($source, $config->targetPath);
            } elseif ($this->isOpenApiType($source->type)) {
                $results[] = $this->syncOpenApiAction->execute($source, $config->targetPath);
            } else {
                $results[] = SyncResultData::failure($source->name, "Unsupported source type: {$source->type}");
            }
        }

        return $results;
    }

    private function isOpenApiType(string $type): bool
    {
        return in_array($type, ['openapi:3', 'openapi:2'], true);
    }
}
