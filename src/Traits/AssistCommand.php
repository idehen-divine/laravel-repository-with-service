<?php

namespace L0n3ly\LaravelRepositoryWithService\Traits;

use Illuminate\Filesystem\Filesystem;

trait AssistCommand
{
    /**
     * Get the app root path
     *
     * @return string|mixed
     */
    public function appPath()
    {
        return app()->basePath();
    }

    /**
     * Ensure a directory exists.
     *
     * @param  string  $path
     * @param  int  $mode
     * @param  bool  $recursive
     * @return void
     */
    public function ensureDirectoryExists($path)
    {
        $resolvedPath = $path;

        if (! str_starts_with($resolvedPath, DIRECTORY_SEPARATOR)) {
            $resolvedPath = rtrim($this->appPath(), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.ltrim($resolvedPath, DIRECTORY_SEPARATOR);
        }

        app()->make(Filesystem::class)->ensureDirectoryExists($resolvedPath);
    }
}
