# Available Commands

## make:model

Overridden Laravel model generation with automatic repository and service creation.

### Syntax

```bash
php artisan make:model {name} {options}
```

### Options

| Option | Shorthand | Description |
|--------|-----------|-------------|
| `--service` | `-sr` | Also generate paired service |
| `--repository` | `-rt` | Also generate paired repository |
| `--all` | `-a` | Generate model, migration, factory, seeder, service, repository |
| `--controller` | `-c` | Create controller |
| `--migration` | `-m` | Create migration |
| `--factory` | `-f` | Create factory |
| `--seeder` | `-s` | Create seeder |
| `--force` | | Overwrite existing files |

### Examples

```bash
# Model only
php artisan make:model User

# Model with repository
php artisan make:model User --repository

# Model with service
php artisan make:model User --service

# Model with both
php artisan make:model Post --service --repository

# Model with everything (enhanced --all)
php artisan make:model Order --all
php artisan make:model Order -a
```

## make:repository

Generate a repository interface and implementation.

### Syntax

```bash
php artisan make:repository {name} {options}
```

### Arguments

| Argument | Description |
|----------|-------------|
| `name` | Repository name (can include subdirectories: `Admin/User`) |

### Options

| Option | Description |
|--------|-------------|
| `--service` | Also generate a paired service class |
| `--api` | Generate API-style service (requires `--service`) |
| `--contract-only` | Generate only the interface |
| `--implementation-only` | Generate only the implementation |
| `--force` | Overwrite existing files |

### Examples

```bash
# Simple repository (wraps Post model)
php artisan make:repository Post

# Repository with paired service
php artisan make:repository User --service

# Repository in subdirectory with service and API template
php artisan make:repository Admin/User --service --api

# Generate from model
php artisan make:model User --repository
```

### Generated Output

Generates two files:

**Interface** (app/Repositories/UserRepository.php):
```php
<?php

namespace App\Repositories;

interface UserRepository
{
    // Define repository methods
}
```

**Implementation** (app/Repositories/UserRepositoryImplement.php):
```php
<?php

namespace App\Repositories;

use App\Models\User;

class UserRepositoryImplement implements UserRepository
{
    // Automatically wraps the User model
    public function __construct(protected User $model) {}

    public function all()
    {
        return $this->model->all();
    }

    public function find($id)
    {
        return $this->model->find($id);
    }
}
```

### Model Wrapping

Repositories automatically wrap models:
- Repository name `UserRepositoryImplement` → wraps `User` model
- Model is resolved from container and injected via constructor
- All repository methods delegate to the wrapped model

## make:service

Generate a service interface and implementation.

### Syntax

```bash
php artisan make:service {name} {options}
```

### Arguments

| Argument | Description |
|----------|-------------|
| `name` | Service name (can include subdirectories: `Admin/User`) |

### Options

| Option | Description |
|--------|-------------|
| `--repository` | Also generate a paired repository class |
| `--api` | Generate API service with response helpers |
| `--blank` | Generate minimal boilerplate |
| `--contract-only` | Generate only the interface |
| `--implementation-only` | Generate only the implementation |
| `--force` | Overwrite existing files |

### Examples

```bash
# Simple service
php artisan make:service Order

# Service with API template
php artisan make:service Order --api

# Service with paired repository
php artisan make:service User --repository

# Blank service (minimal code)
php artisan make:service Report --blank

# Service in feature directory
php artisan make:service Admin/Dashboard --api

# Generate only interface
php artisan make:service Payment --contract-only
```

### Generated Output

**Interface** (app/Services/OrderService.php):
```php
<?php

namespace App\Services;

interface OrderService
{
    // Define service methods
}
```

**Implementation** (app/Services/OrderServiceImplement.php):
```php
<?php

namespace App\Services;

use App\Repositories\OrderRepository;

class OrderServiceImplement implements OrderService
{
    public function __construct(
        protected OrderRepository $repository
    ) {}

    // Implement service methods
}
```

### API Template Output

When using `--api`, generates response helper methods:

```php
class OrderServiceImplement implements OrderService
{
    public function success($data = null, $message = '')
    {
        // Returns formatted success response
    }

    public function error($message = '', $data = null)
    {
        // Returns formatted error response
    }
}
```

## Combined Usage Examples

### Create Blog Module
```bash
# Create repository for blog posts
php artisan make:repository Post --service --api

# Create repository for comments (subdirectory)
php artisan make:repository Blog/Comment --service

# Create service for blog operations
php artisan make:service Blog/BlogManager
```

### Create Admin Module
```bash
# User management
php artisan make:repository Admin/User --service --api

# Role management
php artisan make:repository Admin/Role --service

# Permission management
php artisan make:repository Admin/Permission --service

# Admin dashboard service
php artisan make:service Admin/Dashboard --api
```

### Result

```
app/
├── Repositories/
│   ├── PostRepository.php
│   ├── PostRepositoryImplement.php
│   └── Blog/
│       ├── CommentRepository.php
│       └── CommentRepositoryImplement.php
│   └── Admin/
│       ├── UserRepository.php
│       ├── UserRepositoryImplement.php
│       ├── RoleRepository.php
│       ├── RoleRepositoryImplement.php
│       ├── PermissionRepository.php
│       └── PermissionRepositoryImplement.php
└── Services/
    ├── PostService.php
    ├── PostServiceImplement.php
    ├── Blog/
    │   ├── CommentService.php
    │   └── CommentServiceImplement.php
    ├── Admin/
    │   ├── UserService.php
    │   ├── UserServiceImplement.php
    │   └── DashboardService.php
    │   └── DashboardServiceImplement.php
    └── BlogManager.php
```

## Command Tips

- **Naming**: Use singular names for clarity (`User` not `Users`)
- **Namespacing**: Subdirectories are automatically converted to namespaces
- **Force Overwrite**: Use `--force` to regenerate files
- **Shortcuts**: `--api` combines well with both repositories and services
- **Separation**: Use `--contract-only` or `--implementation-only` for advanced workflows

## See Also

- [Installation](./INSTALLATION.md)
- [Quick Start](./QUICKSTART.md)
- [API Documentation](./API.md)
