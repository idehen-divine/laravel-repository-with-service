<?php

namespace L0n3ly\LaravelRepositoryWithService\Providers;

use L0n3ly\LaravelRepositoryWithService\Console\Commands\MakeRepository;
use L0n3ly\LaravelRepositoryWithService\Console\Commands\MakeService;
use L0n3ly\LaravelRepositoryWithService\Console\Commands\ModelMakeCommand;
use L0n3ly\LaravelRepositoryWithService\Core\ServiceRepository;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class PackageProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-repository-with-service')
            ->hasConfigFile('service-repository')
            ->hasCommand(MakeRepository::class)
            ->hasCommand(MakeService::class);
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(ServiceRepository::class, static fn (): ServiceRepository => new ServiceRepository);
        $this->app->alias(ServiceRepository::class, 'laravel-repository-with-service');

        $this->overrideCommands();
    }

    public function overrideCommands(): void
    {
        $this->app->extend('command.model.make', function () {
            return app()->make(ModelMakeCommand::class);
        });
    }
}
