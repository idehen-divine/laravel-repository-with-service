# API Documentation

Core interfaces, classes, and configuration for the Laravel Repository with Service package.

## Core Interfaces

### BaseService

Base interface for all services. All services should extend this contract.

```php
namespace L0n3ly\LaravelRepositoryWithService\Contracts;

interface BaseService
{
    // Base service methods
}
```

### Repository Contract

Base interface for all repositories.

```php
namespace L0n3ly\LaravelRepositoryWithService\Contracts;

interface Repository
{
    // Base repository methods
}
```

## Service Repository Class

Main service repository handler for dynamic binding.

```php
namespace L0n3ly\LaravelRepositoryWithService\Core;

class ServiceRepository
{
    /**
     * Bind a contract interface to an implementation
     */
    public function bind(string $contract, string $implementation): void;

    /**
     * Resolve a contract from the container
     */
    public function resolve(string $contract): object;

    /**
     * Check if a contract is bound
     */
    public function isBound(string $contract): bool;
}
```

## Facades

### ServiceRepositoryFacade

Quick access to service repository functionality.

```php
use L0n3ly\LaravelRepositoryWithService\Facades\ServiceRepositoryFacade;

// Alias available as
use L0n3lyRepositoryWithService;
```

Usage in code:

```php
// Resolve a service
$userService = L0n3lyRepositoryWithService::resolve(UserService::class);

// Check if bound
if (L0n3lyRepositoryWithService::isBound(UserRepository::class)) {
    // Service is available
}
```

## API Service Template

When using `--api` flag, services include response helpers via `ResultService`:

```php
namespace L0n3ly\LaravelRepositoryWithService\Traits;

trait ResultService
{
    /**
     * Return a successful response
     */
    protected function success($data = null, string $message = ''): array;

    /**
     * Return an error response
     */
    protected function error(string $message = '', $data = null): array;
}
```

Usage in API services:

```php
class UserServiceImplement implements UserService
{
    use ResultService;

    public function create(array $data): array
    {
        try {
            $user = $this->repository->create($data);
            return $this->success($user, 'User created successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
```

## Service Container Integration

### Automatic Resolution

Type-hint interfaces in constructors:

```php
class UserController extends Controller
{
    public function __construct(
        protected UserRepository $repository,
        protected UserService $service
    ) {}

    public function store()
    {
        // Both are automatically resolved from container
    }
}
```

### Manual Resolution

```php
$userService = app(UserService::class);
$userRepository = app(UserRepository::class);
```

### Singleton Binding

By default, services are transient. To bind as singleton:

```php
// In your service provider
$this->app->singleton(UserRepository::class, UserRepositoryImplement::class);
```

## Configuration API

### config/service-repository.php

```php
return [
    // Directory where repositories are stored
    'repository_directory' => 'app/Repositories',

    // Namespace for repository classes
    'repository_namespace' => 'App\Repositories',

    // Directory where services are stored
    'service_directory' => 'app/Services',

    // Namespace for service classes
    'service_namespace' => 'App\Services',

    // Suffix for repository interfaces
    'repository_interface_suffix' => 'Repository',

    // Suffix for repository implementations
    'repository_suffix' => 'RepositoryImplement',

    // Suffix for service interfaces
    'service_interface_suffix' => 'Service',

    // Suffix for service implementations
    'service_suffix' => 'ServiceImplement',
];
```

## Helper Classes

### CreateFileHelper

Handles file creation and class generation.

```php
namespace L0n3ly\LaravelRepositoryWithService\Helpers;

class CreateFileHelper
{
    public static function create(string $path, string $content): bool;
    public static function exists(string $path): bool;
}
```

### SearchHelper

Searches for and resolves class names and paths.

```php
namespace L0n3ly\LaravelRepositoryWithService\Helpers;

class SearchHelper
{
    public static function findClasses(string $directory): Collection;
    public static function resolveNamespace(string $path): string;
}
```

## Advanced Usage

### Custom Binding Decorator

```php
use App\Repositories\UserRepository;
use App\Repositories\CachedUserRepository;

// In your service provider
$this->app->extend(UserRepository::class, function ($original, $app) {
    return new CachedUserRepository($original);
});
```

### Multiple Implementations

```php
// Bind different implementations based on conditions
if ($this->app->environment('production')) {
    $this->app->bind(UserRepository::class, OptimizedUserRepository::class);
} else {
    $this->app->bind(UserRepository::class, UserRepositoryImplement::class);
}
```

### Repository with Eloquent

A typical Eloquent repository implementation:

```php
class UserRepositoryImplement implements UserRepository
{
    public function __construct(protected User $model) {}

    public function all()
    {
        return $this->model->all();
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function create(array $attributes)
    {
        return $this->model->create($attributes);
    }

    public function update($id, array $attributes)
    {
        $model = $this->model->findOrFail($id);
        $model->update($attributes);

        return $model->fresh();
    }

    public function updateOrCreate(array $attributes, array $values = [])
    {
        return $this->model->updateOrCreate($attributes, $values);
    }

    public function firstOrCreate(array $attributes, array $values = [])
    {
        return $this->model->firstOrCreate($attributes, $values);
    }
}
```

## See Also

- [Main Documentation](../README.md) - Installation, quick start, and command reference
- [Troubleshooting](./TROUBLESHOOTING.md) - Common issues and solutions
- [Examples](./EXAMPLES.md) - Real-world usage examples
