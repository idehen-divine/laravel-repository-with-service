# Quick Start Guide

## Fastest Start: Complete Feature Generation

Scaffold a complete feature with one command:

```bash
php artisan make:model User --all
```

This creates:
- Eloquent model
- Database migration, factory, seeder
- Controller
- Repository interface + implementation (wraps model)
- Service interface + implementation (wraps repository)

Then use immediately in your controller:

```php
class UserController extends Controller
{
    public function __construct(protected UserService $service) {}

    public function index()
    {
        return $this->service->all();
    }
}
```

## Step-by-Step Workflow

### 1. Generate a Model and Repository

Create your Eloquent model and a repository that wraps it:

```bash
php artisan make:model User --repository
```

This creates:
```
app/Models/User.php
app/Repositories/
├── UserRepository.php (interface)
└── UserRepositoryImplement.php (wraps User model)
```

### 2. Generate a Service

Generate a service interface and implementation:

```bash
php artisan make:service User
```

This creates:
```
app/Services/
├── UserService.php (interface)
└── UserServiceImplement.php (implementation)
```

### 3. Generate Both Together

Create a repository and service with a single command:

```bash
php artisan make:repository User --service
```

### 4. Use in Your Application

The container automatically binds interfaces to implementations:

```php
<?php

namespace App\Http\Controllers;

use App\Repositories\UserRepository;

class UserController extends Controller
{
    public function __construct(
        protected UserRepository $userRepository
    ) {}

    public function index()
    {
        return $this->userRepository->all();
    }
}
```

## Service Templates

### Standard Service Template

```bash
php artisan make:service UserService
```

Generates:
```php
<?php

namespace App\Services;

use App\Services\UserService as UserServiceContract;

interface UserService
{
    // Define service methods here
}

class UserServiceImplement implements UserService
{
    public function __construct(
        protected UserRepository $userRepository
    ) {}

    // Implement service methods
}
```

### API Service Template

```bash
php artisan make:service UserService --api
```

Generates a service with built-in response helper methods (success, error, etc.)

### Blank Template

```bash
php artisan make:service UserService --blank
```

Generates minimal boilerplate.

## Organizing with Subdirectories

Group repositories and services by feature:

```bash
# Creates app/Repositories/Admin/UserRepository.php
php artisan make:repository Admin/User

# Creates app/Services/Admin/UserService.php
php artisan make:service Admin/User
```

Resulting structure:
```
app/
├── Repositories/
│   └── Admin/
│       ├── UserRepository.php
│       └── UserRepositoryImplement.php
└── Services/
    └── Admin/
        ├── UserService.php
        └── UserServiceImplement.php
```

## Common Patterns

### Model Wrapping

Repositories automatically wrap Eloquent models:

```php
// When you generate repository for "User"
class UserRepositoryImplement implements UserRepository
{
    // Laravel automatically resolves and injects User model
    public function __construct(protected User $model) {}

    public function all()
    {
        return $this->model->all();
    }
}
```

The model wrapping is automatic — just name your repository after your model.

### Single Repository Type

```bash
# Just generate the interface
php artisan make:repository User --contract-only

# Just generate the implementation
php artisan make:repository User --implementation-only
```

### Generate from Model

```bash
# Create model with repository
php artisan make:model Post --repository

# Create model with service
php artisan make:model Post --service

# Create model with both
php artisan make:model Post -sr -rt
```

### Override Bindings

In your application's service provider:

```php
use App\Repositories\UserRepository;
use App\Repositories\UserRepositoryImplement;
use App\Services\CachedUserRepository;

public function register()
{
    $this->app->extend(UserRepository::class, function ($original, $app) {
        return new CachedUserRepository($original);
    });
}
```

### Dependency Injection

Classes are automatically bound via the container:

```php
public function show(UserRepository $repository, UserService $service)
{
    $user = $repository->find(1);
    $data = $service->processUser($user);
    return response()->json($data);
}
```

## Tips & Best Practices

1. **Always use interfaces** - The package enforces interface-first development
2. **Keep services focused** - One service per domain/feature
3. **Test with Contracts** - Type hint against contracts in tests
4. **Use subdirectories** - Organize code by feature or domain
5. **Document contracts** - Add PHPDoc to interface methods

## Troubleshooting

**Repository/Service not binding?**
- Ensure `config/service-repository.php` is published
- Check that classes follow naming conventions
- Verify interface and implementation exist

**Commands not found?**
- Run `php artisan list` to see available commands
- Check Laravel version compatibility (11+)

See [Help & Troubleshooting](./TROUBLESHOOTING.md) for more help.
