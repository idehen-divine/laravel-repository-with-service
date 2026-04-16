# Installation & Setup

## Requirements

- PHP ^8.2
- Laravel 11, 12, or 13
- Composer 2.0+

## Installation Steps

### 1. Install via Composer

```bash
composer require l0n3ly/laravel-repository-with-service
```

### 2. Publish Configuration

```bash
php artisan vendor:publish --tag=repository-with-service-config
```

This creates `config/service-repository.php` in your application.

### 3. Configure Directories (Optional)

Edit `config/service-repository.php` to customize class locations:

```php
return [
    'repository_directory' => 'app/Repositories',      // Where repositories are stored
    'repository_namespace' => 'App\Repositories',       // Repository namespace
    'service_directory' => 'app/Services',             // Where services are stored
    'service_namespace' => 'App\Services',             // Service namespace
    'repository_interface_suffix' => 'Repository',      // Interface suffix (e.g., UserRepository)
    'repository_suffix' => 'RepositoryImplement',       // Implementation suffix
    'service_interface_suffix' => 'Service',            // Interface suffix (e.g., UserService)
    'service_suffix' => 'ServiceImplement',             // Implementation suffix
];
```

### 4. Verify Installation

Run a simple command to verify everything is working:

```bash
php artisan make:repository Test
```

You should see:
- Contract generated at `app/Repositories/TestRepository.php`
- Implementation generated at `app/Repositories/TestRepositoryImplement.php`

## What Gets Installed

### Core Files
- **Artisan Commands** - `make:repository` and `make:service`
- **Service Container Bindings** - Auto-binds interfaces to implementations
- **Contracts** - Base interfaces for repositories and services
- **Facades** - Easy access via `ServiceRepositoryFacade`

### Configuration
- `config/service-repository.php` - Package configuration

## Next Steps

- [Quick Start Guide](./QUICKSTART.md)
- [Available Commands](./COMMANDS.md)
- [API Documentation](./API.md)
