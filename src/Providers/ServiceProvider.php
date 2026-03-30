<?php

namespace L0n3ly\LaravelRepositoryWithService\Providers;

use Carbon\Laravel\ServiceProvider as BaseServiceProvider;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * File
     *
     * @property $files
     */
    private Filesystem $files;

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->files = $this->app->make(Filesystem::class);
        if ($this->isConfigPublished()) {
            $this->bindAllRepositories();
            $this->bindAllServices();
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Loop through the repository interfaces and bind each interface to its
     * Repository inside the implementations
     *
     * @return void
     */
    private function bindAllRepositories()
    {
        $this->bindContractsForDirectory(
            config('service-repository.repository_directory'),
            config('service-repository.repository_namespace'),
            config('service-repository.repository_interface_suffix'),
            config('service-repository.repository_suffix')
        );
    }

    /**
     * bind all service
     */
    private function bindAllServices()
    {
        $this->bindContractsForDirectory(
            config('service-repository.service_directory'),
            config('service-repository.service_namespace'),
            config('service-repository.service_interface_suffix'),
            config('service-repository.service_suffix')
        );
    }

    /**
     * Recursively bind contract classes to implementation classes.
     */
    private function bindContractsForDirectory(
        string $directory,
        string $namespace,
        string $contractSuffix,
        string $implementationSuffix
    ): void {
        $root = $this->app->basePath().'/'.trim($directory, '/');

        if (! is_dir($root)) {
            return;
        }

        $files = File::allFiles($root);

        foreach ($files as $file) {
            $fileName = $file->getFilename();

            if (! str_ends_with($fileName, $contractSuffix.'.php') || str_ends_with($fileName, $implementationSuffix.'.php')) {
                continue;
            }

            $contractBaseName = pathinfo($fileName, PATHINFO_FILENAME);
            $entityBaseName = substr($contractBaseName, 0, -strlen($contractSuffix));

            if ($entityBaseName === false || $entityBaseName === '') {
                continue;
            }

            $implementationBaseName = $entityBaseName.$implementationSuffix;

            $relativePath = str_replace('/', '\\', $file->getRelativePath());

            $contractClass = $this->qualifyClassName($namespace, $relativePath, $contractBaseName);
            $implementationClass = $this->qualifyClassName($namespace, $relativePath, $implementationBaseName);

            if (interface_exists($contractClass) && class_exists($implementationClass)) {
                $this->app->bind($contractClass, $implementationClass);
            }
        }
    }

    private function qualifyClassName(string $baseNamespace, string $relativePath, string $className): string
    {
        $qualified = $baseNamespace.'\\'.($relativePath !== '' ? $relativePath.'\\' : '').$className;

        return str_replace('\\\\', '\\', $qualified);
    }

    /**
     * Check inside the repositories interfaces directory and get all interfaces
     *
     * @return Collection
     */
    public function getRepository()
    {
        $root = $this->app->basePath().'/'.trim(config('service-repository.repository_directory'), '/');

        if (! is_dir($root)) {
            return collect([]);
        }

        return collect(File::allFiles($root))
            ->map(fn ($file) => pathinfo($file->getFilename(), PATHINFO_FILENAME))
            ->filter(fn ($name) => str_ends_with($name, config('service-repository.repository_interface_suffix'))
                && ! str_ends_with($name, config('service-repository.repository_suffix')))
            ->values();
    }

    /**
     * Check if config is published
     *
     * @return bool
     */
    private function isConfigPublished()
    {
        $path = config_path('service-repository.php');
        $exists = file_exists($path);

        return $exists;
    }
}
