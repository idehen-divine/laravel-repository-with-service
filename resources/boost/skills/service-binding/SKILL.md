---
name: service-binding
description: Understand and implement dependency injection, service binding, and Laravel container patterns for repositories and services.
---

# Service Binding & Dependency Injection

This skill helps you understand how service binding and dependency injection works in the Laravel Repository With Service package.

## When to use this skill

Use this skill when:

- Understanding how dependencies are resolved
- Configuring custom service bindings
- Testing with mocks and fakes
- Troubleshooting resolution issues
- Implementing advanced binding patterns

## Automatic Binding

The package automatically binds three types of classes:

### 1. Model Resolution

Models are resolved from container by class name:

```
Repository Name             → Model Resolved From Container
UserRepositoryImplement     → User (App\Models\User)
PostRepositoryImplement     → Post (App\Models\Post)
```

### 2. Repository Binding

Repositories bind interface to implementation:

```
Interface Name              → Implementation Name
UserRepository              → UserRepositoryImplement
Admin/UserRepository        → Admin/UserRepositoryImplement
Blog/CommentRepository      → Blog/CommentRepositoryImplement
```

### 3. Service Binding

Services bind interface to implementation:

```
Interface Name              → Implementation Name
UserService                 → UserServiceImplement
Admin/UserService           → Admin/UserServiceImplement
Blog/CommentService         → Blog/CommentServiceImplement
```

No configuration needed - happens automatically! The binding chain:

- **Service** → injects **Repository interface** → resolved to **Repository implementation**
- **Repository** → injects **Model class** → resolved from container

## Service Container Binding

### Automatic Resolution

Type-hint in constructor:

```php
class UserController extends Controller
{
    public function __construct(
        protected UserRepository $repository,
        protected UserService $service
    ) {}

    public function index()
    {
        // Both automatically resolved from container
        return $this->service->getActiveUsers();
    }
}
```

### Manual Resolution

```php
$userService = app(UserService::class);
$users = $userService->getActiveUsers();

// Or using make()
$userService = app()->make(UserService::class);

// Or using resolve()
$userService = resolve(UserService::class);
```

## Binding Verification

### Check if Bound

```php
if (app()->bound(UserRepository::class)) {
    // Service is bound
    $repo = app(UserRepository::class);
}
```

### Solve Resolution Issues

```php
// Clear cache if issues
php artisan config:clear

// Verify configuration is published
php artisan vendor:publish --tag=repository-with-service-config

// Check binding in tinker
php> app()->bound('App\Repositories\UserRepository')
```

## Advanced Binding Patterns

### Singleton Binding

Make service always return the same instance:

```php
// In your AppServiceProvider
public function register()
{
    $this->app->singleton(UserRepository::class, UserRepositoryImplement::class);
}
```

Usage:

```php
app(UserRepository::class) === app(UserRepository::class) // true (same instance)
```

### Conditional Binding

Bind different implementations based on environment or conditions:

```php
public function register()
{
    if ($this->app->environment('production')) {
        $this->app->bind(UserRepository::class, CachedUserRepository::class);
    } else {
        $this->app->bind(UserRepository::class, UserRepositoryImplement::class);
    }
}
```

### Decorator Pattern

Wrap an implementation with additional functionality:

```php
public function register()
{
    $this->app->extend(UserRepository::class, function ($original, $app) {
        return new LoggingUserRepository($original);
    });
}

// Decorator class
class LoggingUserRepository implements UserRepository
{
    public function __construct(
        protected UserRepository $repository,
        protected Logger $logger
    ) {}

    public function find($id)
    {
        $this->logger->info("Finding user: $id");
        return $this->repository->find($id);
    }
}
```

### Context-Based Binding

Bind different implementations based on context:

```php
public function register()
{
    // When OrderService needs PaymentRepository
    $this->app->when(OrderService::class)
        ->needs(PaymentRepository::class)
        ->give(StripePaymentRepository::class);

    // When AdminService needs PaymentRepository
    $this->app->when(AdminService::class)
        ->needs(PaymentRepository::class)
        ->give(ManualPaymentRepository::class);
}
```

## Testing with Bindings

### Mock Repository in Tests

```php
public function test_service_creates_user()
{
    // Create mock
    $mock = Mockery::mock(UserRepository::class);
    $mock->shouldReceive('create')
        ->with(Mockery::type('array'))
        ->andReturn(['id' => 1, 'name' => 'Test']);

    // Bind mock to container
    $this->app->bind(UserRepository::class, fn() => $mock);

    // Service uses the mock
    $service = app(UserService::class);
    $result = $service->create(['name' => 'Test']);

    $this->assertEquals(1, $result['data']['id']);
}
```

### Using Fake Repository

```php
class FakeUserRepository implements UserRepository
{
    public function find($id)
    {
        return ['id' => $id, 'name' => 'Test User'];
    }
}

// In test
$this->app->bind(UserRepository::class, FakeUserRepository::class);
$service = app(UserService::class);
$user = $service->find(1);
```

### Using instance()

```php
$mock = Mockery::mock(UserRepository::class);
$mock->shouldReceive('all')->andReturn([]);

$this->instance(UserRepository::class, $mock);

$service = app(UserService::class);
```

## Dependency Injection in Services

### Constructor Injection

```php
class OrderServiceImplement implements OrderService
{
    public function __construct(
        protected OrderRepository $orders,
        protected ItemRepository $items,
        protected InventoryService $inventory
    ) {}

    public function checkout($id)
    {
        $order = $this->orders->find($id);
        $items = $this->items->where('order_id', $id)->get();
        return $this->inventory->process($items);
    }
}
```

### Method Injection

```php
public function process(OrderRepository $orders, PaymentService $payment)
{
    // Dependencies are automatically injected
    $order = $orders->find(1);
    return $payment->charge($order);
}
```

### Route Model Binding

```php
Route::get('/users/{user}', function (User $user, UserService $service) {
    // $user is resolved from route parameter
    // $service is resolved from container
    return $service->getDetails($user);
});
```

## Subdirectory Bindings

### Feature Organization

```
app/Repositories/
├── Admin/
│   ├── UserRepository.php
│   └── UserRepositoryImplement.php
├── Blog/
│   ├── PostRepository.php
│   └── PostRepositoryImplement.php
```

Generate with:

```bash
php artisan make:repository Admin/User --service
php artisan make:repository Blog/Post --service
```

Reference naturally:

```php
use App\Repositories\Admin\UserRepository;
use App\Repositories\Blog\PostRepository;

class AdminUserController
{
    public function __construct(
        protected UserRepository $users  // Admin\UserRepository
    ) {}
}
```

Binding is automatic (full namespace):

```
App\Repositories\Admin\UserRepository → App\Repositories\Admin\UserRepositoryImplement
```

## Troubleshooting Bindings

### Service Not Resolving

1. **Check if config is published**

    ```bash
    php artisan vendor:publish --tag=repository-with-service-config
    ```

2. **Verify files exist**

    ```bash
    ls app/Repositories/UserRepository.php
    ls app/Repositories/UserRepositoryImplement.php
    ```

3. **Check naming convention**
    - Interface: `UserRepository`
    - Implementation: `UserRepositoryImplement`
    - Must match naming suffix from config

4. **Clear caches**

    ```bash
    php artisan config:clear
    php artisan cache:clear
    ```

5. **Verify namespace matches config**

    ```php
    // config/service-repository.php
    'repository_namespace' => 'App\Repositories'

    // Files must be in App\Repositories namespace
    namespace App\Repositories;
    ```

### Testing Resolution

```php
// In tinker
php> app()->bound('App\Repositories\UserRepository')
=> true

php> app('App\Repositories\UserRepository')
=> UserRepositoryImplement { ... }

php> get_class(app('App\Repositories\UserRepository'))
=> "App\Repositories\UserRepositoryImplement"
```

## Configuration

Customize binding behavior in `config/service-repository.php`:

```php
return [
    // Where to find repositories
    'repository_directory' => 'app/Repositories',
    'repository_namespace' => 'App\Repositories',

    // Where to find services
    'service_directory' => 'app/Services',
    'service_namespace' => 'App\Services',

    // Naming conventions for binding
    'repository_interface_suffix' => 'Repository',
    'repository_suffix' => 'RepositoryImplement',
    'service_interface_suffix' => 'Service',
    'service_suffix' => 'ServiceImplement',
];
```

## Common Binding Example

```php
// AppServiceProvider.php
public function register()
{
    // Production: Use caching decorator
    if ($this->app->environment('production')) {
        $this->app->extend(UserRepository::class, function ($original) {
            return new CachedUserRepository($original);
        });
    }
}

public function boot()
{
    // Singleton for stateless services
    $this->app->singleton(
        ConfigService::class,
        ConfigServiceImplement::class
    );
}
```

Then everywhere you use:

```php
// Automatically gets the bound implementation
class UserController
{
    public function __construct(protected UserRepository $repository) {}
}
```
