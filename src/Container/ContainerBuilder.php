<?php

declare(strict_types=1);

namespace Kyprss\AiDocs\Container;

use Kyprss\AiDocs\Commands\InitCommand;
use Kyprss\AiDocs\Commands\SyncCommand;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use Symfony\Component\DependencyInjection\ContainerBuilder as SymfonyContainerBuilder;

final class ContainerBuilder
{
    public static function build(): SymfonyContainerBuilder
    {
        $container = new SymfonyContainerBuilder();

        // Register all classes in the base namespace for autowiring
        self::registerNamespaceServices($container, __DIR__.'/../', 'Kyprss\\AiDocs\\');

        // Only register commands as public services (needed for console access)
        $container->register(InitCommand::class)
            ->setAutowired(true)
            ->setPublic(true);

        $container->register(SyncCommand::class)
            ->setAutowired(true)
            ->setPublic(true);

        $container->compile();

        return $container;
    }

    private static function registerNamespaceServices(SymfonyContainerBuilder $container, string $baseDir, string $namespace): void
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($baseDir, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $relativePath = str_replace($baseDir, '', $file->getPathname());
            $relativePath = str_replace('/', '\\', $relativePath);
            $className = $namespace.str_replace('.php', '', $relativePath);

            // Skip commands (already registered) and container classes
            if (str_contains($className, 'Commands\\') || str_contains($className, 'Container\\')) {
                continue;
            }

            if (class_exists($className) && ! interface_exists($className) && ! trait_exists($className)) {
                $reflection = new ReflectionClass($className);
                if (! $reflection->isAbstract() && ! $reflection->isInterface()) {
                    $container->register($className)
                        ->setAutowired(true);
                }
            }
        }
    }
}
