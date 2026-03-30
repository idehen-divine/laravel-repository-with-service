<?php

namespace L0n3ly\LaravelRepositoryWithService\Core;

use Illuminate\Support\Str;

class ServiceRepository
{
    /**
     * Simple health method to verify facade wiring.
     */
    public function ping(): string
    {
        return 'laravel-repository-with-service';
    }

    /**
     * Return key package defaults from config.
     */
    public function defaults(): array
    {
        return [
            'repository_directory' => (string) config('service-repository.repository_directory', 'app/Repositories'),
            'repository_namespace' => (string) config('service-repository.repository_namespace', 'App\\Repositories'),
            'service_directory' => (string) config('service-repository.service_directory', 'app/Services'),
            'service_namespace' => (string) config('service-repository.service_namespace', 'App\\Services'),
        ];
    }

    /**
     * Build a repository interface FQCN from a model/entity name.
     */
    public function repositoryInterfaceFor(string $name): string
    {
        $entity = Str::studly(trim($name));

        return (string) config('service-repository.repository_namespace', 'App\\Repositories')
            .'\\'
            .$entity
            .'\\'
            .$entity
            .(string) config('service-repository.repository_interface_suffix', 'Repository');
    }

    /**
     * Build a service interface FQCN from a model/entity name.
     */
    public function serviceInterfaceFor(string $name): string
    {
        $entity = Str::studly(trim($name));

        return (string) config('service-repository.service_namespace', 'App\\Services')
            .'\\'
            .$entity
            .'\\'
            .$entity
            .(string) config('service-repository.service_interface_suffix', 'Service');
    }

    /**
     * Build both interface and implementation FQCNs for an entity.
     */
    public function bindingsFor(string $name): array
    {
        $entity = Str::studly(trim($name));

        $repositoryBase = (string) config('service-repository.repository_namespace', 'App\\Repositories')
            .'\\'
            .$entity
            .'\\'
            .$entity;

        $serviceBase = (string) config('service-repository.service_namespace', 'App\\Services')
            .'\\'
            .$entity
            .'\\'
            .$entity;

        return [
            'repository_interface' => $repositoryBase.(string) config('service-repository.repository_interface_suffix', 'Repository'),
            'repository_implementation' => $repositoryBase.(string) config('service-repository.repository_suffix', 'RepositoryImplement'),
            'service_interface' => $serviceBase.(string) config('service-repository.service_interface_suffix', 'Service'),
            'service_implementation' => $serviceBase.(string) config('service-repository.service_suffix', 'ServiceImplement'),
        ];
    }
}
