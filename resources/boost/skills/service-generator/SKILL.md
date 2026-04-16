---
name: service-generator
description: Generate and work with service classes for implementing business logic, orchestrating repositories, and managing application workflows.
---

# Service Generator

This skill helps you generate and work with service classes using the Laravel Repository With Service package.

## When to use this skill

Use this skill when:

- Creating services for business logic implementation
- Orchestrating multiple repositories
- Implementing API responses with consistent formatting
- Building application workflows and transactions
- Managing complex multi-step operations

## Primary Commands

### Generate a basic service

```bash
php artisan make:service UserService
```

Creates:

- `app/Services/UserService.php` (interface)
- `app/Services/UserServiceImplement.php` (implementation)

### Generate with API template

```bash
php artisan make:service Order --api
```

Includes `ResultService` trait for response helpers

### Generate with paired repository

```bash
php artisan make:service Product --repository
```

### Generate from a Model

```bash
# Model with auto-generated service and repository
php artisan make:model Order --service
php artisan make:model Product -sr -rt  # With repository too
```

### Generate in subdirectory

```bash
php artisan make:service Shop/Checkout --api
php artisan make:service Admin/Dashboard --api
```

## Service Structure with Repository Injection

Services automatically receive injected repositories:

```php
class PostServiceImplement implements PostService
{
    use ResultService;  // For API template

    // Repository is automatically resolved from container
    public function __construct(
        protected PostRepository $repository,
        protected CommentRepository $commentRepository  // Can inject multiple
    ) {}
}
```

The service wraps the repository, which wraps the model. This creates three layers:

1. **Controller** - HTTP request handling
2. **Service** - Business logic orchestration (wraps repositories)
3. **Repository** - Data access layer (wraps models)
4. **Model** - Eloquent database layer

## Service Structure

### Interface Pattern

```php
namespace App\Services;

interface UserService
{
    public function createUser(array $data): array;
    public function updateUser($id, array $data): array;
    public function getActiveUsers(): array;
    public function deleteUser($id): array;
}
```

### Implementation Pattern (Standard)

```php
namespace App\Services;

use App\Repositories\UserRepository;
use Exception;

class UserServiceImplement implements UserService
{
    public function __construct(
        protected UserRepository $repository
    ) {}

    public function createUser(array $data): array
    {
        try {
            $user = $this->repository->create($data);
            return ['success' => true, 'data' => $user];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
```

### API Service Pattern (with --api flag)

```php
namespace App\Services;

use App\Repositories\UserRepository;
use L0n3ly\LaravelRepositoryWithService\Traits\ResultService;

class UserServiceImplement implements UserService
{
    use ResultService;  // Provides success/error helpers

    public function __construct(
        protected UserRepository $repository
    ) {}

    public function createUser(array $data): array
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

## ResultService Response Helpers

The `ResultService` trait provides formatted responses:

```php
// Success response
return $this->success($data, 'Operation successful');
// Returns: ['success' => true, 'data' => $data, 'message' => '...']

// Error response
return $this->error('Error message', $data = null);
// Returns: ['success' => false, 'error' => '...', 'data' => null]
```

## Common Service Methods

### CRUD Operations

```php
class UserServiceImplement implements UserService
{
    use ResultService;

    public function __construct(protected UserRepository $repository) {}

    public function all(): array
    {
        try {
            $users = $this->repository->all();
            return $this->success($users);
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function find($id): array
    {
        try {
            $user = $this->repository->find($id);
            if (!$user) {
                return $this->error('User not found');
            }
            return $this->success($user);
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function create(array $data): array
    {
        try {
            // Validate
            if (empty($data['email'])) {
                return $this->error('Email is required');
            }

            $user = $this->repository->create($data);
            return $this->success($user, 'User created');
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function update($id, array $data): array
    {
        try {
            $user = $this->repository->update($id, $data);
            return $this->success($user, 'User updated');
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function delete($id): array
    {
        try {
            $success = $this->repository->delete($id);
            if ($success) {
                return $this->success(null, 'User deleted');
            }
            return $this->error('Failed to delete user');
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
```

### Business Logic Operations

```php
class OrderServiceImplement implements OrderService
{
    use ResultService;

    public function __construct(
        protected OrderRepository $orders,
        protected ItemRepository $items,
        protected InventoryService $inventory
    ) {}

    public function checkout(array $cartItems): array
    {
        try {
            // Create order
            $order = $this->orders->create([
                'user_id' => auth()->id(),
                'total' => $this->calculateTotal($cartItems),
            ]);

            // Add items
            foreach ($cartItems as $item) {
                $this->items->create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                ]);
            }

            // Update inventory
            $this->inventory->decrementFor($cartItems);

            return $this->success($order, 'Order created');
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    private function calculateTotal(array $items): float
    {
        return collect($items)
            ->sum(fn($item) => $item['price'] * $item['quantity']);
    }
}
```

## Best Practices

1. **Inject Dependencies**

    ```php
    public function __construct(
        protected UserRepository $repository,
        protected AuditService $audit
    ) {}
    ```

2. **Use Result Service for APIs**

    ```php
    use ResultService;
    return $this->success($data, 'Message');
    return $this->error('Error message');
    ```

3. **Validate Data**

    ```php
    if (empty($data['name'])) {
        return $this->error('Name is required');
    }
    ```

4. **Handle Exceptions**

    ```php
    try {
        // operation
    } catch (Exception $e) {
        return $this->error($e->getMessage());
    }
    ```

5. **Type Everything**

    ```php
    public function create(array $data): array
    public function find($id): array
    ```

6. **Document Methods**
    ```php
    /**
     * Create a new user
     *
     * @param array $data
     * @return array
     */
    public function create(array $data): array
    ```

## Advanced Patterns

### Transaction Handling

```php
class CheckoutServiceImplement implements CheckoutService
{
    use ResultService;

    public function processOrder(array $items): array
    {
        try {
            return DB::transaction(function () use ($items) {
                $order = $this->createOrder($items);
                $this->addItems($order, $items);
                $this->updateInventory($items);
                return $this->success($order);
            });
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
```

### Multi-Repository Orchestration

```php
class DashboardServiceImplement implements DashboardService
{
    use ResultService;

    public function __construct(
        protected UserRepository $users,
        protected OrderRepository $orders,
        protected ProductRepository $products
    ) {}

    public function getDashboardData(): array
    {
        try {
            return $this->success([
                'total_users' => $this->users->count(),
                'total_orders' => $this->orders->count(),
                'recent_users' => $this->users->latest()->limit(5)->get(),
                'recent_orders' => $this->orders->latest()->limit(5)->get(),
            ]);
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
```

### Event Dispatching

```php
public function createUser(array $data): array
{
    try {
        $user = $this->repository->create($data);
        event(new UserCreated($user));
        return $this->success($user, 'User created');
    } catch (Exception $e) {
        return $this->error($e->getMessage());
    }
}
```

### Caching

```php
public function getActiveUsers(): array
{
    return cache()->remember('users:active', 3600, function () {
        $users = $this->repository->active();
        return $this->success($users);
    });
}
```

## Testing Services

```php
class UserServiceTest extends TestCase
{
    protected UserService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(UserService::class);
    }

    public function test_can_create_user()
    {
        $response = $this->service->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->assertTrue($response['success']);
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    public function test_returns_error_for_invalid_data()
    {
        $response = $this->service->create(['name' => 'Test']);
        $this->assertFalse($response['success']);
    }
}
```

## Controller Integration

```php
class UserController extends Controller
{
    public function __construct(protected UserService $service) {}

    public function store(Request $request)
    {
        $response = $this->service->create($request->validated());

        if (!$response['success']) {
            return response()->json($response, 422);
        }

        return response()->json($response, 201);
    }
}
```
