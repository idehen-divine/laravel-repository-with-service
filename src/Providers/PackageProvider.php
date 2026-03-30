<?php

namespace L0n3ly\LaravelRepositoryWithService\Providers;

use L0n3ly\LaravelRepositoryWithService\Console\Commands\MakeRepository;
use L0n3ly\LaravelRepositoryWithService\Console\Commands\MakeService;
use L0n3ly\LaravelRepositoryWithService\Console\Commands\ModelMakeCommand;
use L0n3ly\LaravelRepositoryWithService\Core\ServiceRepository;
use Spatie\LaravelPackageTools\Exceptions\InvalidPackage;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class PackageProvider extends PackageServiceProvider
{
    public function register()
    {
        $this->registeringPackage();

        $this->package = new Package;

        $this->package->setBasePath($this->getPackageBaseDir());

        $this->configurePackage($this->package);

        if (empty($this->package->name)) {
            throw InvalidPackage::nameIsRequired();
        }

        foreach ($this->package->configFileNames as $configFileName) {
            $this->mergeConfigFrom($this->package->basePath("/../config/{$configFileName}.php"), $configFileName);
        }

        $this->packageRegistered();

        $this->app->singleton(ServiceRepository::class, static fn (): ServiceRepository => new ServiceRepository);
        $this->app->alias(ServiceRepository::class, 'laravel-repository-with-service');

        $this->overrideCommands();

        return $this;
    }

    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-repository-with-service')
            ->hasConfigFile('service-repository')
            ->hasCommand(MakeRepository::class)
            ->hasCommand(MakeService::class);
    }

    public function overrideCommands()
    {
        $this->app->extend('command.model.make', function () {
            return app()->make(ModelMakeCommand::class);
        });
    }
}
