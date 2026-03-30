<?php

namespace L0n3ly\LaravelRepositoryWithService\Tests\Unit;

use L0n3ly\LaravelRepositoryWithService\Tests\TestCase;

/**
 * @group unit
 * Class RepositoryTest
 */
class RepositoryTest extends TestCase
{
    private $suffix;

    protected function setUp(): void
    {
        parent::setUp();
        $this->suffix = 'User';
    }

    public function test_create_repository_interface()
    {
        $this->artisan("make:repository {$this->suffix}")->assertSuccessful();

        $filePath = $this->app->basePath()
            .'/'
            .config('service-repository.repository_directory')
            ."/{$this->suffix}/{$this->suffix}"
            .config('service-repository.repository_interface_suffix')
            .'.php';

        $this->assertFileExists($filePath);
    }

    public function test_create_repository()
    {
        $this->artisan("make:repository {$this->suffix}")->assertSuccessful();

        $filePath = $this->app->basePath()
            .'/'
            .config('service-repository.repository_directory')
            ."/{$this->suffix}/{$this->suffix}"
            .config('service-repository.repository_suffix')
            .'.php';

        $this->assertFileExists($filePath);
    }
}
