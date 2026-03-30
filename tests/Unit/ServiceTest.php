<?php

namespace L0n3ly\LaravelRepositoryWithService\Tests\Unit;

use Illuminate\Support\Str;
use L0n3ly\LaravelRepositoryWithService\Tests\TestCase;

/**
 * @group unit
 * Class ServiceTest
 */
class ServiceTest extends TestCase
{
    private $suffix;

    protected function setUp(): void
    {
        parent::setUp();
        $this->suffix = 'User';
    }

    public function test_create_service_interface()
    {
        $this->artisan("make:service {$this->suffix}")->assertSuccessful();

        $filePath = $this->app->basePath()
            .'/'
            .config('service-repository.service_directory')
            ."/{$this->suffix}/{$this->suffix}"
            .config('service-repository.service_interface_suffix')
            .'.php';

        $this->assertFileExists($filePath);
    }

    public function test_create_service()
    {
        $this->artisan("make:service {$this->suffix}")->assertSuccessful();

        $filePath = $this->app->basePath()
            .'/'
            .config('service-repository.service_directory')
            ."/{$this->suffix}/{$this->suffix}"
            .config('service-repository.service_suffix')
            .'.php';

        $this->assertFileExists($filePath);
    }

    /**
     * test simulation create generate name of service
     */
    public function test_class_name_generate()
    {
        $input = 'Setting/OpenServiceImplement';
        $name = str_replace(config('service-repository.service_suffix'), '', $input);
        $className = Str::studly($name);

        $this->assertEquals($name, $className);
    }

    /**
     * test create namespace on service
     */
    public function test_make_namespace()
    {

        $className = 'Book/Category';
        $namespace = '';

        $explode = explode('/', $className);
        if (count($explode) > 1) {
            $namespace = '';
            for ($i = 0; $i < count($explode) - 1; $i++) {
                $namespace .= '\\'.$explode[$i];
            }
            $namespace = config('service-repository.service_namespace').$namespace.'\\'.end($explode);
        } else {
            $namespace = config('service-repository.service_namespace').'\\'.$className;
        }

        $this->assertStringEndsWith('Category', $namespace);
    }
}
