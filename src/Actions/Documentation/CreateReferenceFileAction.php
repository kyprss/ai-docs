<?php

declare(strict_types=1);

namespace Kyprss\AiDocs\Actions\Documentation;

use Kyprss\AiDocs\Exceptions\FileSystemException;
use Kyprss\AiDocs\Services\FileSystemService;

final class CreateReferenceFileAction
{
    public function __construct(
        private readonly FileSystemService $fileSystemService
    ) {}

    /**
     * @throws FileSystemException
     */
    public function execute(string $name, array $files, string $destDir, string $tempDir): void
    {
        $content = "# {$name} Documentation\n\n";
        $content .= "This file contains references to all {$name} documentation files.\n\n";

        foreach ($files as $file) {
            $relativePath = str_replace($tempDir.'/', '', $file);
            $content .= "- [{$relativePath}](docs/{$relativePath})\n";
        }

        $this->fileSystemService->dumpFile($destDir."/{$name}.md", $content);
    }
}
