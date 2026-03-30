<?php

namespace L0n3ly\LaravelRepositoryWithService\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use L0n3ly\LaravelRepositoryWithService\Helpers\CreateFileHelper;
use L0n3ly\LaravelRepositoryWithService\Traits\AssistCommand;

class MakeRepository extends Command
{
    use AssistCommand;

    public $signature = 'make:repository
        {name : The name of the repository }
        {--other : If not put, it will create an eloquent repository}?
        {--service : Create a service along with the repository}?';

    public $description = 'Create a new repository class';

    /**
     * Handle the command
     *
     * @return void
     */
    public function handle()
    {
        $name = str_replace(config('service-repository.repository_interface_suffix'), '', $this->argument('name'));
        $name = Str::studly($name);

        $other = $this->option('other');

        $this->checkIfRequiredDirectoriesExist($name);

        // First we create the repoisitory interface in the interfaces directory
        // This will be implemented by the interface class
        $this->createRepositoryInterface($name);

        // Second we create the repoisitory directory
        // This will be implement by the interface class
        $this->createRepository($name, ! $other);

        if ($this->option('service')) {
            $this->createService();
        }
    }

    /**
     * Create service for the repository
     *
     * @return void
     */
    private function createService()
    {
        $name = str_replace(config('service-repository.repository_interface_suffix'), '', $this->argument('name'));
        $name = Str::studly($name);

        $this->call('make:service', [
            'name' => $name,
        ]);
    }

    /**
     * Create the repository interface
     *
     * @return string|void
     */
    public function createRepositoryInterface(string $name)
    {
        $className = $this->getEntityName($name);
        $namespace = $this->getNameSpace($name);
        $repositoryInterfaceName = $className.config('service-repository.repository_interface_suffix');
        $stubProperties = [
            '{namespace}' => $namespace,
            '{repositoryInterfaceName}' => $repositoryInterfaceName,
        ];

        $repositoryInterfacePath = $this->getRepositoryInterfacePath($name);
        if (file_exists($repositoryInterfacePath)) {
            $this->error("file $className repository interface already exist");

            return;
        }

        new CreateFileHelper(
            $stubProperties,
            $repositoryInterfacePath,
            __DIR__.'/stubs/repository-interface.stub'
        );
        $this->line("<info>Created $className repository interface:</info> ".$repositoryInterfaceName);

        return $namespace.'\\'.$className;
    }

    /**
     * Create repository
     *
     * @return string|void
     */
    public function createRepository(string $name, $isDefault = true)
    {
        $className = $this->getEntityName($name);
        $namespace = $this->getNameSpace($name);
        $repositoryName = $className.config('service-repository.repository_suffix');
        $stubProperties = [
            '{namespace}' => $namespace,
            '{repositoryName}' => $repositoryName,
            '{repositoryInterfaceName}' => $className.config('service-repository.repository_interface_suffix'),
            '{ModelName}' => $className,
        ];

        $stubName = $isDefault ? 'eloquent-repository.stub' : 'custom-repository.stub';
        $repositoryPath = $this->getRepositoryPath($name, $isDefault);
        if (file_exists($repositoryPath)) {
            $this->error("file $className repository already exist");

            return;
        }
        new CreateFileHelper(
            $stubProperties,
            $repositoryPath,
            __DIR__."/stubs/$stubName"
        );
        $this->line("<info>Created $className repository implement:</info> ".$repositoryName);

        return $namespace.'\\'.$className;
    }

    /**
     * Get the entity class name (last segment of the path)
     */
    private function getEntityName(string $name): string
    {
        $segments = explode('/', $name);

        return end($segments);
    }

    /**
     * Build the fully-qualified namespace, including any subdirectory segments
     */
    private function getNameSpace(string $name): string
    {
        $segments = explode('/', $name);
        if (count($segments) > 1) {
            $namespace = '';
            for ($i = 0; $i < count($segments) - 1; $i++) {
                $namespace .= '\\'.$segments[$i];
            }

            return config('service-repository.repository_namespace').$namespace.'\\'.end($segments);
        }

        return config('service-repository.repository_namespace').'\\'.end($segments);
    }

    /**
     * Get repository interface path
     *
     * @return string
     */
    private function getRepositoryInterfacePath(string $name): string
    {
        $className = $this->getEntityName($name);

        return $this->appPath().'/'.
            config('service-repository.repository_directory').
            "/{$name}/{$className}".config('service-repository.repository_interface_suffix').'.php';
    }

    /**
     * Get repository path
     *
     * @return string
     */
    private function getRepositoryPath(string $name, bool $isDefault): string
    {
        $className = $this->getEntityName($name);
        $path = $isDefault
            ? "/{$name}/{$className}".config('service-repository.repository_suffix').'.php'
            : "/Other/{$className}".config('service-repository.repository_suffix').'.php';

        return $this->appPath().'/'.
            config('service-repository.repository_directory').$path;
    }

    /**
     * Check to make sure if all required directories are available
     *
     * @return void
     */
    private function checkIfRequiredDirectoriesExist(string $name)
    {
        $this->ensureDirectoryExists(config('service-repository.repository_directory'));
        $segments = explode('/', $name);
        $path = config('service-repository.repository_directory');
        foreach ($segments as $segment) {
            $path .= '/'.$segment;
            $this->ensureDirectoryExists($path);
        }
    }
}
