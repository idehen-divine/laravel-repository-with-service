<!-- Boost AI Guidelines for Laravel Repository With Service Package -->

# Laravel Repository With Service

This package provides a complete repository and service pattern scaffolding system for Laravel applications.

## Features

- **Repository Pattern** - Data access layer with automatic service container binding
- **Service Layer** - Business logic orchestration with built-in support for API responses
- **Code Generation** - Artisan commands to generate repositories and services with interfaces
- **Subdirectory Support** - Organize code by feature or domain
- **Auto-Binding** - Automatic dependency injection without manual configuration
- **Multiple Templates** - Standard, API, and blank templates for services

## Required File Structure

```
app/
├── Repositories/
│ ├── {Entity}Repository.php (interface)
│ └── {Entity}RepositoryImplement.php (implementation)
└── Services/
├── {Entity}Service.php (interface)
└── {Entity}ServiceImplement.php (implementation)
```

## Generating Models with Repositories & Services

The package overrides Laravel's `make:model` command to auto-generate repositories and services:

```bash
# Model only
php artisan make:model User

# Model with service
php artisan make:model User --service

# Model with repository
php artisan make:model User --repository

# Model with both (enhanced --all flag)
php artisan make:model User --all
php artisan make:model User -a -sr -rt # Shorthand
```

This creates:
- Eloquent Model
- Migration, Factory, Seeder (with `--all`)
- Repository interface + implementation
- Service interface + implementation

## Generating Repositories

Use the `make:repository` Artisan command:

```bash
# Basic repository
php artisan make:repository User

# With paired service and API template
php artisan make:repository Post --service --api

# In subdirectory
php artisan make:repository Admin/User --service
```

Generated files automatically:
- Create interface and implementation
- Wrap the corresponding Eloquent Model
- Follow naming conventions
- Bind to service container

## Generating Services

Use the `make:service` Artisan command:

```bash
# Basic service
php artisan make:service User

# With API template (includes response helpers)
php artisan make:service Order --api

# With paired repository
php artisan make:service Product --repository
```

## Repository Best Practices

1. **Type Everything** - Use type hints for all parameters and return values
2. **Use Eloquent Models** - Inject models into constructor
3. **Keep Focused** - One repository per domain entity
4. **Document Methods** - Add PHPDoc to public methods
5. **Use Subdirectories** - Group related repositories

Example:
```php
class UserRepositoryImplement implements UserRepository
{
// Model is automatically injected by Laravel's container
public function __construct(protected User $model) {}

public function all(): Collection
{
return $this->model->all();
}

public function active(): Collection
{
return $this->model->where('active', true)->get();
}
}
```

The repository **wraps the Eloquent Model** - the model is automatically resolved from the container based on the
repository name and injected into the constructor.

## Service Best Practices

1. **Inject Dependencies** - Repositories and other services via constructor
2. **Handle Exceptions** - Wrap operations in try-catch for API services
3. **Use ResultService Trait** - For consistent response formatting (with `--api`)
4. **Type Return Values** - Always specify return types
5. **Keep Business Logic Separate** - Services orchestrate, repositories access data

Example:
```php
class UserServiceImplement implements UserService
{
// Repository is auto-injected; no configuration needed
public function __construct(protected UserRepository $repository) {}
use ResultService; // Provides response helpers

public function __construct(protected UserRepository $repository) {}

public function create(array $data): array
{
try {
$user = $this->repository->create($data);
return $this->success($user, 'User created');
} catch (Exception $e) {
return $this->error($e->getMessage());
}
}
}
```

## Dependency Injection

Services and repositories are automatically bound to the container via the package:

```php
class UserController extends Controller
{
// Both are automatically resolved
public function __construct(
protected UserRepository $repository,
protected UserService $service
) {}
}
```

## Service Binding

The binding follows a convention:
- Interface: `{Entity}Repository` or `{Entity}Service`
- Implementation: `{Entity}RepositoryImplement` or `{Entity}ServiceImplement`

Automatic binding in container:
```
UserRepository → UserRepositoryImplement
UserService → UserServiceImplement
```

## Common Commands

```bash
# Generate repository with service
php artisan make:repository Post --service

# Generate API service
php artisan make:service Order --api

# Generate both together
php artisan make:repository Product --service --api

# Subdirectory organization
php artisan make:repository Admin/User --service
php artisan make:repository Blog/Post --service --api
```

## Configuration

Edit `config/service-repository.php`:

```php
return [
'repository_directory' => 'app/Repositories',
'repository_namespace' => 'App\Repositories',
'service_directory' => 'app/Services',
'service_namespace' => 'App\Services',
'repository_interface_suffix' => 'Repository',
'repository_suffix' => 'RepositoryImplement',
'service_interface_suffix' => 'Service',
'service_suffix' => 'ServiceImplement',
];
```

## Common Patterns

### Blog Application
- PostRepository with PostService
- CommentRepository with CommentService
- CategoryRepository with CategoryService

### E-Commerce
- ProductRepository with ProductService
- OrderRepository with OrderService
- Shop/CheckoutService for transactions

### Multi-Tenant SaaS
- TenantRepository with scoped queries
- UserRepository with TenantId scoping
- SubscriptionService for billing

## Advanced Usage

### Custom Bindings

Decorate implementations in service provider:

```php
$this->app->extend(UserRepository::class, function ($original) {
return new CachedUserRepository($original);
});
```

### Transaction Handling

Use DB transactions in services for multi-step operations:

```php
class CheckoutService
{
public function process(array $items): array
{
return DB::transaction(function () use ($items) {
// Create order
// Add items
// Update inventory
return $this->success($order);
});
}
}
```

### Query Optimization

Use eager loading in repositories:

```php
public function withComments(): Collection
{
return $this->model->with('comments')->get();
}
```

## Testing

Mock repositories in tests:

```php
public function test_service_creates_user()
{
$mock = Mockery::mock(UserRepository::class);
$mock->shouldReceive('create')->andReturn(['id' => 1]);

$this->app->bind(UserRepository::class, fn() => $mock);

$service = app(UserService::class);
$result = $service->create(['name' => 'Test']);
}
```

## Reference

- Installation: `php artisan vendor:publish --tag=repository-with-service-config`
- Commands: `php artisan make:repository {name}` or `php artisan make:service {name}`
- Documentation: See package documentation at github.com/l0n3ly/laravel-repository-with-service
