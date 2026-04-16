# API Documentation

## Model Make Command Override

The package overrides Laravel's `make:model` command to support automatic repository and service generation:

```bash
php artisan make:model User                           # Model only
php artisan make:model User --service                 # Model + service
php artisan make:model User --repository              # Model + repository
php artisan make:model User --service --repository    # Model + both
php artisan make:model User --all                     # Model + migration + factory + seeder + service + repository
```

### Model Wrapping Pattern

When you generate a repository, it automatically wraps the corresponding Eloquent model:

```php
class UserRepositoryImplement implements UserRepository
{
    public function __construct(protected User $model) {}

    public function all()
    {
        return $this->model->all();
    }
}
```

The repository name determines which model to wrap. The model is resolved from the service container by class name matching.

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

## Generated Repository Structure

When you run `make:repository User`, the following structure is created:

### Interface (UserRepository.php)

```php
<?php

namespace App\Repositories;

interface UserRepository
{
    // Define repository contract methods
}
```

### Implementation (UserRepositoryImplement.php)

```php
<?php

namespace App\Repositories;

use App\Models\User;

class UserRepositoryImplement implements UserRepository
{
    public function __construct(protected User $model) {}

    public function all()
    {
        return $this->model->all();
    }
}
```

### Auto-Binding

The package automatically binds three levels:

**Level 1 - Model Resolution**:
UserRepositoryImplement → injects User model

**Level 2 - Repository Binding**:
UserRepository (interface) → UserRepositoryImplement (implementation)

**Level 3 - Service Binding**:
UserService (interface) → UserServiceImplement (implementation) → injects repository

## Generated Service Structure

When you run `make:service User`, the following is created:

### Interface (UserService.php)

```php
<?php

namespace App\Services;

interface UserService
{
    // Define service contract methods
}
```

### Implementation (UserServiceImplement.php)

```php
<?php

namespace App\Services;

class UserServiceImplement implements UserService
{
    public function __construct(
        protected UserRepository $repository
    ) {}

    // Implement service methods
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

```php
class UserRepositoryImplement implements UserRepository
{
    public function __construct(
        protected User $model = null
    ) {
        $this->model = $model ?? new User();
    }

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
}
```

## See Also

- [Installation](./INSTALLATION.md)
- [Quick Start](./QUICKSTART.md)
- [Commands](./COMMANDS.md)
