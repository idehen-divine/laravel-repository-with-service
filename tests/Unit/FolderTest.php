<?php

namespace L0n3ly\LaravelRepositoryWithService\Tests\Unit;

use Illuminate\Filesystem\Filesystem;
use L0n3ly\LaravelRepositoryWithService\Tests\TestCase;

class FolderTest extends TestCase
{
    /**
     * @group folder_test
     */
    public function test_folder_repository()
    {
        $this->app->make(Filesystem::class)->ensureDirectoryExists(
            $this->app->basePath().'/'.config('service-repository.repository_directory')
        );

        $folder_exist = false;
        if (file_exists($this->app->basePath().'/'.config('service-repository.repository_directory'))) {
            $folder_exist = true;
        } else {
            $folder_exist = false;
        }

        $this->assertEquals(true, $folder_exist);
    }
}
