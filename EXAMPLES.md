# Examples & Real-World Use Cases

## 0. Fastest Start: Complete Feature Generation

Scaffold an entire feature with one command:

```bash
php artisan make:model Post --all
```

This single command creates:
- ✅ `app/Models/Post.php` (Eloquent model)
- ✅ Database migration, factory, seeder
- ✅ `app/Http/Controllers/PostController.php`
- ✅ `app/Repositories/PostRepository.php` (interface)
- ✅ `app/Repositories/PostRepositoryImplement.php` (wraps Post model)
- ✅ `app/Services/PostService.php` (interface)
- ✅ `app/Services/PostServiceImplement.php` (wraps PostRepository)

Now use immediately in your controller:

```php
class PostController extends Controller
{
    public function __construct(protected PostService $service) {}

    public function index()
    {
        return $this->service->all();
    }
}
```

## 1. Blog Application

### Generate Blog Structure

```bash
# Post management
php artisan make:repository Post --service --api
php artisan make:repository Category --service
php artisan make:repository Tag --service

# Comment management
php artisan make:repository Post/Comment --service --api

# Author/user management
php artisan make:service Blog/PostPublisher --api
```

### Generated Structure

```
app/
├── Repositories/
│   ├── PostRepository.php
│   ├── PostRepositoryImplement.php
│   ├── CategoryRepository.php
│   ├── CategoryRepositoryImplement.php
│   ├── TagRepository.php
│   ├── TagRepositoryImplement.php
│   └── Post/
│       ├── CommentRepository.php
│       └── CommentRepositoryImplement.php
└── Services/
    ├── PostService.php
    ├── PostServiceImplement.php
    ├── CategoryService.php
    ├── CategoryServiceImplement.php
    ├── TagService.php
    ├── TagServiceImplement.php
    ├── Post/
    │   ├── CommentService.php
    │   └── CommentServiceImplement.php
    └── Blog/
        ├── PostPublisher.php
        └── PostPublisherImplement.php
```

### Implementation Example

**Repository** - Data access:

```php
<?php

namespace App\Repositories;

use App\Models\Post;

interface PostRepository
{
    public function all();
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function published();
}

class PostRepositoryImplement implements PostRepository
{
    public function __construct(protected Post $model) {}

    public function all()
    {
        return $this->model->all();
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function published()
    {
        return $this->model->where('published', true)
            ->orderByDesc('published_at')
            ->get();
    }
}
```

**Service** - Business logic:

```php
<?php

namespace App\Services;

use App\Repositories\PostRepository;
use App\Repositories\Post\CommentRepository;

interface PostService
{
    public function publishPost($id);
    public function getLatestPosts($limit = 10);
    public function addComment($postId, array $data);
}

class PostServiceImplement implements PostService
{
    use ResultService;

    public function __construct(
        protected PostRepository $postRepository,
        protected CommentRepository $commentRepository
    ) {}

    public function publishPost($id): array
    {
        try {
            $post = $this->postRepository->update($id, [
                'published' => true,
                'published_at' => now(),
            ]);

            return $this->success($post, 'Post published');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function getLatestPosts($limit = 10): array
    {
        try {
            $posts = $this->postRepository->published()
                ->limit($limit)
                ->get();

            return $this->success($posts);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function addComment($postId, array $data): array
    {
        try {
            $comment = $this->commentRepository->create([
                ...$data,
                'post_id' => $postId,
            ]);

            return $this->success($comment);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
```

**Controller** - Request handling:

```php
<?php

namespace App\Http\Controllers;

use App\Services\PostService;

class PostController extends Controller
{
    public function __construct(protected PostService $postService) {}

    public function index()
    {
        $response = $this->postService->getLatestPosts(15);
        return response()->json($response);
    }

    public function publish($id)
    {
        $response = $this->postService->publishPost($id);
        return response()->json($response);
    }

    public function comment($id, Request $request)
    {
        $response = $this->postService->addComment($id, $request->validated());
        return response()->json($response);
    }
}
```

## 2. E-Commerce Platform

### Generate E-Commerce Structure

```bash
# Products
php artisan make:repository Product --service --api
php artisan make:repository Product/Variant --service

# Orders
php artisan make:repository Order --service --api
php artisan make:repository Order/Item --service

# Users/Customers
php artisan make:repository Customer --service --api
php artisan make:repository Customer/Address --service

# Business Logic
php artisan make:service Shop/Checkout --api
php artisan make:service Shop/Inventory --api
php artisan make:service Shop/Payment --api
```

### Order Processing Example

```php
<?php

namespace App\Services\Shop;

use App\Repositories\OrderRepository;
use App\Repositories\Order\ItemRepository;
use App\Repositories\CustomerRepository;

class CheckoutServiceImplement implements CheckoutService
{
    use ResultService;

    public function __construct(
        protected OrderRepository $orderRepository,
        protected ItemRepository $itemRepository,
        protected CustomerRepository $customerRepository,
    ) {}

    public function checkout(array $cart): array
    {
        try {
            // Create order
            $order = $this->orderRepository->create([
                'customer_id' => auth()->id(),
                'total' => $this->calculateTotal($cart),
                'status' => 'pending',
            ]);

            // Add items
            foreach ($cart as $item) {
                $this->itemRepository->create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
            }

            return $this->success($order, 'Order created');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    private function calculateTotal(array $cart): float
    {
        return collect($cart)
            ->sum(fn ($item) => $item['price'] * $item['quantity']);
    }
}
```

## 3. SaaS Application with Multi-Tenancy

### Generate Multi-Tenant Structure

```bash
# Tenant management
php artisan make:repository Tenant --service --api

# User & Role management
php artisan make:repository User --service --api
php artisan make:repository Role --service
php artisan make:repository Permission --service

# Billing
php artisan make:repository Subscription --service --api
php artisan make:repository Invoice --service --api

# App features
php artisan make:service Feature/Analytics --api
php artisan make:service Feature/Reporting --api
```

### Multi-Tenant Service Example

```php
<?php

namespace App\Services;

use App\Repositories\SubscriptionRepository;

class SubscriptionServiceImplement implements SubscriptionService
{
    use ResultService;

    public function __construct(
        protected SubscriptionRepository $repository
    ) {}

    public function upgradeSubscription($tenantId, $plan): array
    {
        try {
            $subscription = $this->repository->where('tenant_id', $tenantId)->first();

            if (!$subscription) {
                return $this->error('Subscription not found');
            }

            // Handle upgrade logic
            $subscription->update([
                'plan' => $plan,
                'upgraded_at' => now(),
            ]);

            // Trigger upgrade event
            event(new SubscriptionUpgraded($subscription));

            return $this->success($subscription, 'Subscription upgraded');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
```

## 4. Admin Dashboard

### Generate Admin Structure

**Option 1: Using make:model (Fastest)**
```bash
php artisan make:model User --all       # Admin users
php artisan make:model Report --all     # Reports
php artisan make:model Settings --all   # Settings

# Dashboard service
php artisan make:service Admin/Dashboard --api
```

**Option 2: Using make:repository (More Control)**
```bash
# Admin features
php artisan make:repository Admin/User --service --api
php artisan make:repository Admin/Report --service --api
php artisan make:repository Admin/Settings --service

# Dashboard
php artisan make:service Admin/Dashboard --api
```

### Admin Dashboard Service

```php
<?php

namespace App\Services\Admin;

class DashboardServiceImplement implements DashboardService
{
    use ResultService;

    public function __construct(
        protected UserRepository $userRepository,
        protected OrderRepository $orderRepository,
    ) {}

    public function getDashboardData(): array
    {
        try {
            $data = [
                'total_users' => $this->userRepository->count(),
                'total_orders' => $this->orderRepository->count(),
                'recent_users' => $this->userRepository->latest()->limit(5)->get(),
                'recent_orders' => $this->orderRepository->latest()->limit(5)->get(),
            ];

            return $this->success($data);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
```

## 5. Testing Example

### Test Repository with Service

```php
<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Repositories\UserRepository;
use App\Services\UserService;

class UserServiceTest extends TestCase
{
    protected UserRepository $repository;
    protected UserService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(UserRepository::class);
        $this->service = app(UserService::class);
    }

    public function test_can_create_user()
    {
        $response = $this->service->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $this->assertTrue($response['success']);
        $this->assertDatabaseHas('users', ['email' => 'john@example.com']);
    }

    public function test_service_handles_errors()
    {
        $response = $this->service->create([
            'name' => 'John',
            // Missing email
        ]);

        $this->assertFalse($response['success']);
        $this->assertNotEmpty($response['error']);
    }
}
```

## 6. API Route Example

### Using Services in Routes

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;

Route::middleware('api')->group(function () {
    // Posts
    Route::get('/posts', [PostController::class, 'index']);
    Route::post('/posts', [PostController::class, 'store']);
    Route::get('/posts/{id}', [PostController::class, 'show']);
    Route::put('/posts/{id}', [PostController::class, 'update']);
    Route::delete('/posts/{id}', [PostController::class, 'destroy']);

    // Publishing
    Route::post('/posts/{id}/publish', [PostController::class, 'publish']);
    Route::post('/posts/{id}/unpublish', [PostController::class, 'unpublish']);

    // Comments
    Route::post('/posts/{id}/comments', [PostController::class, 'addComment']);
    Route::delete('/comments/{id}', [PostController::class, 'deleteComment']);
});
```

## 7. Custom Binding Example

### Override Services in Production

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\UserRepository;
use App\Repositories\CachedUserRepository;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Use cached version in production
        if ($this->app->environment('production')) {
            $this->app->extend(UserRepository::class, function ($original) {
                return new CachedUserRepository($original);
            });
        }
    }
}
```

These examples demonstrate real-world usage patterns. Adapt them to your specific needs!

---

See [Documentation](./docs) for more information.
