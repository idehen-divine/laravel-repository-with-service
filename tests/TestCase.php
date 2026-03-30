<?php

namespace L0n3ly\LaravelRepositoryWithService\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use L0n3ly\LaravelRepositoryWithService\Providers\PackageProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'L0n3ly\LaravelRepositoryWithService\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            PackageProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
    }
}
