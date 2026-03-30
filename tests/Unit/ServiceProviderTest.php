<?php

namespace L0n3ly\LaravelRepositoryWithService\Tests\Unit;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use L0n3ly\LaravelRepositoryWithService\Helpers\SearchHelper;
use L0n3ly\LaravelRepositoryWithService\Tests\TestCase;

class ServiceProviderTest extends TestCase
{
    private Filesystem $files;

    protected function setUp(): void
    {
        parent::setUp();
        $this->files = $this->app->make(Filesystem::class);
    }

    public function test_schema_bind_repository()
    {
        $this->artisan('make:repository User')->assertSuccessful();

        $dirs = File::directories($this->app->basePath().'/'.config('service-repository.repository_directory'));
        $folders = [];
        foreach ($dirs as $dir) {
            $arr = explode('/', $dir);
            $folders[] = end($arr);
        }

        $repositoryServiceProvider = [];
        foreach ($folders as $repositoryInterface) {
            $repositoryInterfaceClass = config('service-repository.repository_namespace').'\\'
                .$repositoryInterface.'\\'
                .$repositoryInterface
                .config('service-repository.repository_interface_suffix');

            $repositoryImplementClass = config('service-repository.repository_namespace').'\\'
                .$repositoryInterface.'\\'
                .$repositoryInterface
                .config('service-repository.repository_suffix');

            $repositoryServiceProvider[] = [$repositoryInterfaceClass, $repositoryImplementClass];
        }

        $this->assertArrayHasKey(0, $repositoryServiceProvider);
    }

    public function test_schema_bind_service()
    {
        $this->artisan('make:service User')->assertSuccessful();

        $root = $this->app->basePath().'/'.config('service-repository.service_directory');
        $path = SearchHelper::file($root, ['php']);

        $servicePath = [];
        foreach ($path as $file) {
            $servicePath[] = str_replace('Services/', '', strstr($file->getPath(), 'Services'));
        }

        $servicePath = array_unique($servicePath);
        $serviceProvider = [];

        foreach ($servicePath as $serviceName) {
            $splitname = explode('/', $serviceName);
            $className = end($splitname);

            $pathService = str_replace('/', '\\', $serviceName);

            $serviceInterfaceClass = config('service-repository.service_namespace').'\\'
                .$pathService.'\\'
                .$className
                .config('service-repository.service_interface_suffix');

            $serviceImplementClass = config('service-repository.service_namespace').'\\'
                .$pathService.'\\'
                .$className
                .config('service-repository.service_suffix');

            $serviceProvider[] = [$serviceInterfaceClass, $serviceImplementClass];
        }

        $this->assertArrayHasKey(0, $serviceProvider);
    }
}
