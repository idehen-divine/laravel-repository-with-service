<?php

namespace L0n3ly\LaravelRepositoryWithService\Tests\Unit;

use L0n3ly\LaravelRepositoryWithService\Core\ServiceRepository;
use L0n3ly\LaravelRepositoryWithService\Facades\ServiceRepositoryFacade;
use L0n3ly\LaravelRepositoryWithService\Helpers\CreateFileHelper;
use L0n3ly\LaravelRepositoryWithService\Helpers\SearchHelper;
use L0n3ly\LaravelRepositoryWithService\Tests\TestCase;

class CoreAndHelpersTest extends TestCase
{
    public function test_container_alias_resolves_core_service_repository(): void
    {
        $resolved = $this->app->make('laravel-repository-with-service');

        $this->assertInstanceOf(ServiceRepository::class, $resolved);
    }

    public function test_facade_calls_core_methods(): void
    {
        $this->assertSame('laravel-repository-with-service', ServiceRepositoryFacade::ping());

        $defaults = ServiceRepositoryFacade::defaults();
        $this->assertArrayHasKey('repository_directory', $defaults);
        $this->assertArrayHasKey('service_directory', $defaults);
    }

    public function test_bindings_for_returns_expected_class_names(): void
    {
        $bindings = ServiceRepositoryFacade::bindingsFor('user');

        $this->assertSame('App\\Repositories\\User\\UserRepository', $bindings['repository_interface']);
        $this->assertSame('App\\Repositories\\User\\UserRepositoryImplement', $bindings['repository_implementation']);
        $this->assertSame('App\\Services\\User\\UserService', $bindings['service_interface']);
        $this->assertSame('App\\Services\\User\\UserServiceImplement', $bindings['service_implementation']);
    }

    public function test_create_file_helper_writes_populated_stub(): void
    {
        $tmpDir = sys_get_temp_dir().'/lrws_'.uniqid('', true);
        mkdir($tmpDir, 0777, true);

        $stubPath = $tmpDir.'/example.stub';
        $targetPath = $tmpDir.'/Example.php';

        file_put_contents($stubPath, "<?php\nclass {name} {}\n");

        new CreateFileHelper([
            '{name}' => 'UserRepository',
        ], $targetPath, $stubPath);

        $this->assertFileExists($targetPath);
        $this->assertStringContainsString('class UserRepository {}', file_get_contents($targetPath));
    }

    public function test_search_helper_finds_matching_extension_files(): void
    {
        $tmpDir = sys_get_temp_dir().'/lrws_search_'.uniqid('', true);
        mkdir($tmpDir, 0777, true);

        file_put_contents($tmpDir.'/one.php', "<?php\n");
        file_put_contents($tmpDir.'/two.txt', "text\n");

        $files = SearchHelper::file($tmpDir, ['php']);

        $this->assertNotEmpty($files);
        $this->assertStringEndsWith('one.php', (string) $files[0]);
    }
}
