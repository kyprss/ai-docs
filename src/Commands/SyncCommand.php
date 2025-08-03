<?php

declare(strict_types=1);

namespace Kyprss\AiDocs\Commands;

use DirectoryIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

final class SyncCommand extends Command
{
    protected static $defaultName = 'sync';

    protected static $defaultDescription = 'Sync documentation from configured sources';

    private Filesystem $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        parent::__construct('sync');
        $this->setDescription('Sync documentation from configured sources');
        $this->filesystem = $filesystem;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $configPath = getcwd().'/ai-docs.json';

        if (! file_exists($configPath)) {
            $io->error('Configuration file ai-docs.json not found. Run "ai-docs init" first.');

            return Command::FAILURE;
        }

        $config = json_decode(file_get_contents($configPath), true);

        if (! $config) {
            $io->error('Invalid configuration file.');

            return Command::FAILURE;
        }

        $targetPath = $config['config']['target_path'] ?? '.ai/docs/';
        $sources = $config['sources'] ?? [];

        $this->cleanupObsoleteDirs($targetPath, $sources, $io);

        foreach ($sources as $name => $source) {
            $io->section("Syncing: {$name}");

            if ($source['type'] !== 'repository') {
                $io->warning("Skipping {$name}: unsupported type '{$source['type']}'");

                continue;
            }

            $this->syncRepository($name, $source, $targetPath, $io);
        }

        $io->success('All sources synced successfully.');

        return Command::SUCCESS;
    }

    private function syncRepository(string $name, array $source, string $targetPath, SymfonyStyle $io): void
    {
        $tempDir = sys_get_temp_dir().'/ai-docs-'.$name.'-'.uniqid();
        $destDir = mb_rtrim($targetPath, '/').'/'.$name;

        try {
            $cloneCommand = ['git', 'clone', '--depth', '1'];

            if (isset($source['branch'])) {
                $cloneCommand[] = '--branch';
                $cloneCommand[] = $source['branch'];
            }

            $cloneCommand[] = $source['url'];
            $cloneCommand[] = $tempDir;

            $process = new Process($cloneCommand);
            $process->run();

            if (! $process->isSuccessful()) {
                $io->error('Failed to clone repository: '.$process->getErrorOutput());

                return;
            }

            $this->filesystem->remove($destDir);
            $this->filesystem->mkdir($destDir.'/docs');

            $files = $this->findMatchingFiles($tempDir, $source['files'] ?? ['*.md']);

            foreach ($files as $file) {
                $relativePath = str_replace($tempDir.'/', '', $file);
                $destFile = $destDir.'/docs/'.$relativePath;

                // Ensure the destination directory exists
                $this->filesystem->mkdir(dirname($destFile));
                $this->filesystem->copy($file, $destFile);
            }

            $this->createReferenceFile($name, $files, $destDir, $tempDir);

            $io->text("Synced {$name}: ".count($files).' files');

        } finally {
            if ($this->filesystem->exists($tempDir)) {
                $this->filesystem->remove($tempDir);
            }
        }
    }

    private function findMatchingFiles(string $dir, array $patterns): array
    {
        $files = [];

        if (! is_dir($dir)) {
            return $files;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($iterator as $file) {
            $filePath = $file->getPathname();
            $fileName = $file->getFilename();

            foreach ($patterns as $pattern) {
                if (fnmatch($pattern, $fileName)) {
                    $files[] = $filePath;
                    break; // Avoid adding the same file multiple times if it matches multiple patterns
                }
            }
        }

        return array_unique($files);
    }

    private function createReferenceFile(string $name, array $files, string $destDir, string $tempDir): void
    {
        $content = "# {$name} Documentation\n\n";
        $content .= "This file contains references to all {$name} documentation files.\n\n";

        foreach ($files as $file) {
            $relativePath = str_replace($tempDir.'/', '', $file);
            $content .= "- [{$relativePath}](docs/{$relativePath})\n";
        }

        $this->filesystem->dumpFile($destDir."/{$name}.md", $content);
    }

    private function cleanupObsoleteDirs(string $targetPath, array $sources, SymfonyStyle $io): void
    {
        $targetDir = mb_rtrim($targetPath, '/');

        if (! $this->filesystem->exists($targetDir)) {
            return;
        }

        $configuredSources = array_keys($sources);
        $existingDirs = [];

        $iterator = new DirectoryIterator($targetDir);
        foreach ($iterator as $fileInfo) {
            if ($fileInfo->isDot() || ! $fileInfo->isDir()) {
                continue;
            }
            $existingDirs[] = $fileInfo->getFilename();
        }

        $obsoleteDirs = array_diff($existingDirs, $configuredSources);

        foreach ($obsoleteDirs as $obsoleteDir) {
            $dirPath = $targetDir.'/'.$obsoleteDir;
            $io->text("Removing obsolete documentation: {$obsoleteDir}");
            $this->filesystem->remove($dirPath);
        }
    }
}
