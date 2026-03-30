<?php

namespace L0n3ly\LaravelRepositoryWithService\Tests\Unit;

use Illuminate\Filesystem\Filesystem;
use L0n3ly\LaravelRepositoryWithService\Tests\TestCase;

class FolderTest extends TestCase
{
    /**
     * Test that the repository directory exists after ensuring it.
     */
    public function test_folder_repository()
    {
        $this->app->make(Filesystem::class)->ensureDirectoryExists(
            $this->app->basePath() . '/' . config('service-repository.repository_directory')
        );

        $folderPath = $this->app->basePath() . '/' . config('service-repository.repository_directory');
        $this->assertTrue(file_exists($folderPath), 'Repository directory should exist.');
    }
}
