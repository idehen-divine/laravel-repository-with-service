---
name: repository-generator
description: Generate and work with repository classes for managing data access, implementing CRUD operations, and organizing repositories by feature.
---

# Repository Generator

This skill helps you generate and work with repository classes using the Laravel Repository With Service package.

## When to use this skill

Use this skill when:

- Creating repositories for data access patterns
- Implementing CRUD operations
- Organizing repositories by feature or domain
- Setting up query methods with proper Eloquent patterns
- Need to establish data access layers

## Primary Commands

### Generate a basic repository

```bash
php artisan make:repository User
```

Creates:

- `app/Repositories/UserRepository.php` (interface)
- `app/Repositories/UserRepositoryImplement.php` (implementation)

### Generate with paired service

```bash
php artisan make:repository Post --service
php artisan make:repository User --service --api
```

### Generate from a Model

```bash
# Model with auto-generated repository and service
php artisan make:model User --repository
php artisan make:model Post -sr -rt  # With service too
```

### Generate in subdirectory

```bash
php artisan make:repository Admin/User --service
php artisan make:repository Blog/Comment --service
```

## Model Wrapping

Repositories automatically wrap an Eloquent Model. The model is:

- Resolved from the service container by class name
- Injected into the repository constructor
- Used for all data access operations

The pattern:

```php
// Generated for repository named "UserRepositoryImplement"
class UserRepositoryImplement implements UserRepository
{
    // Laravel automatically resolves and injects the User model
    public function __construct(protected User $model) {}
    // ...
}
```

## Repository Structure

### Interface Pattern

```php
namespace App\Repositories;

interface UserRepository
{
    public function all(): Collection;
    public function find($id): ?User;
    public function create(array $data): User;
    public function update($id, array $data): User;
    public function delete($id): bool;
}
```

### Implementation Pattern

```php
namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Collection;

class UserRepositoryImplement implements UserRepository
{
    public function __construct(protected User $model) {}

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function find($id): ?User
    {
        return $this->model->find($id);
    }

    public function create(array $data): User
    {
        return $this->model->create($data);
    }

    public function update($id, array $data): User
    {
        $model = $this->model->find($id);
        $model->update($data);
        return $model;
    }

    public function delete($id): bool
    {
        return $this->model->find($id)?->delete() ?? false;
    }
}
```

## Common Methods

### Query Methods

```php
// Get with relationships
public function withComments(): Collection
{
    return $this->model->with('comments')->get();
}

// Filter by status
public function active(): Collection
{
    return $this->model->where('active', true)->get();
}

// Paginated results
public function paginate($perPage = 15)
{
    return $this->model->paginate($perPage);
}

// Search functionality
public function search($query): Collection
{
    return $this->model->where('name', 'like', "%$query%")->get();
}
```

### Relationship Methods

```php
// With related data
public function findWithComments($id): ?User
{
    return $this->model->with('comments')->find($id);
}

// Load relationships
public function allWithRelations(): Collection
{
    return $this->model->with(['comments', 'posts'])->get();
}
```

## Best Practices

1. **Type Everything**

    ```php
    public function find($id): ?User
    public function all(): Collection
    public function create(array $data): User
    ```

2. **Use Single Responsibility**
    - One repository per model/domain entity
    - Never mix concerns

3. **Document Methods**

    ```php
    /**
     * Get all active users
     *
     * @return Collection
     */
    public function active(): Collection
    ```

4. **Use Eager Loading**

    ```php
    // Good
    public function allWithComments(): Collection
    {
        return $this->model->with('comments')->get();
    }

    // Avoid N+1 queries
    ```

5. **Create Query Scopes**

    ```php
    public function published(): Collection
    {
        return $this->model->where('published', true)->get();
    }

    public function drafts(): Collection
    {
        return $this->model->where('published', false)->get();
    }
    ```

## Feature Organization

Use subdirectories by feature:

```
app/Repositories/
├── Admin/
│   ├── UserRepository.php
│   └── UserRepositoryImplement.php
├── Blog/
│   ├── PostRepository.php
│   ├── PostRepositoryImplement.php
│   ├── CommentRepository.php
│   └── CommentRepositoryImplement.php
└── Shop/
    ├── ProductRepository.php
    ├── OrderRepository.php
    └── (implementations)
```

Generate with:

```bash
php artisan make:repository Admin/User --service
php artisan make:repository Blog/Post --service
php artisan make:repository Shop/Product --service
```

## Advanced Patterns

### Using Scopes in Repository

```php
class PostRepositoryImplement implements PostRepository
{
    public function published(): Collection
    {
        return $this->model
            ->published()  // uses Eloquent scope
            ->orderByDesc('published_at')
            ->get();
    }
}
```

### Query Builder Methods

```php
public function activeInMonth($month): Collection
{
    return $this->model
        ->whereMonth('created_at', $month)
        ->where('status', 'active')
        ->get();
}
```

### Pagination and Filtering

```php
public function search($term, $page = 1)
{
    return $this->model
        ->where('title', 'like', "%$term%")
        ->orWhere('description', 'like', "%$term%")
        ->paginate(15, ['*'], 'page', $page);
}
```

## Integration with Services

Repositories are automatically injected into services:

```php
class PostServiceImplement implements PostService
{
    public function __construct(
        protected PostRepository $repository,
        protected CommentRepository $comments
    ) {}

    public function publishPost($id)
    {
        $post = $this->repository->find($id);
        $post->update(['published' => true]);
        return $post;
    }
}
```

And then used in controllers:

```php
class PostController
{
    public function __construct(protected PostService $service) {}

    public function index()
    {
        return $this->service->getAllPosts();
    }
}
```
