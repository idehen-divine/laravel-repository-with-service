# Troubleshooting Guide

## Common Issues & Solutions

### Command Not Found

**Problem**: `make:repository` or `make:service` command not found

**Solution**:
```bash
# Clear cached commands
php artisan clear:cache
php artisan clear-compiled

# Verify package is installed
composer show l0n3ly/laravel-repository-with-service

# List all available commands
php artisan list
```

### Models Not Auto-Wrapping in Repositories

**Problem**: Repository cannot resolve the model ("Target class [User] does not exist")

**Solution**:
- Repository name must match model name: `UserRepositoryImplement` → `User` model
- Model must be in `app/Models/` directory (Laravel default)
- For custom locations, ensure model is registered in container
- Run `php artisan make:model User` first, then `make:repository User`

```bash
# Correct order:
php artisan make:model User          # Creates model
php artisan make:repository User     # Wraps the model

# Or use combined command:
php artisan make:model User --repository  # Both at once
```

### Configuration Not Published

**Problem**: Classes are not being auto-bound to the container

**Solution**:
```bash
# Publish the configuration file
php artisan vendor:publish --tag=repository-with-service-config

# Verify config file exists
ls config/service-repository.php

# Clear config cache
php artisan config:clear
```

### Model Make Command Not Generating Repository/Service

**Problem**: `php artisan make:model User --service` doesn't create service

**Solution**:
- Ensure package is installed: `composer require l0n3ly/laravel-repository-with-service`
- Package overrides `make:model` command with `ModelMakeCommand` class
- Run `php artisan list` to verify command override is loaded
- Check app service provider registers `PackageProvider`
- Clear cache: `php artisan clear:cache && php artisan clear-compiled`

```bash
# Troubleshoot with verbose output
php artisan make:model User --service --verbose
```

### Classes Not Auto-Binding

**Problem**: Interface not resolving to implementation despite being generated

**Checklist**:
- [ ] Config is published: `php artisan vendor:publish --tag=repository-with-service-config`
- [ ] Interface exists: `app/Repositories/UserRepository.php`
- [ ] Implementation exists: `app/Repositories/UserRepositoryImplement.php`
- [ ] Naming follows conventions:
  - Interface: `{Name}Repository` or `{Name}Service`
  - Implementation: `{Name}RepositoryImplement` or `{Name}ServiceImplement`
- [ ] Namespace matches config: `app/Repositories` and `App\Repositories`

**Solution**:

Clear the cache and regenerate:
```bash
php artisan config:clear
php artisan make:repository User --force
```

### Composer Dependency Issues

**Problem**: `illuminate/contracts` version conflicts

**Solution**:
```bash
# Clean and reinstall
composer remove l0n3ly/laravel-repository-with-service
composer install
composer require l0n3ly/laravel-repository-with-service
```

### PHP Version Incompatibility

**Problem**: "Minimum PHP version is 8.2"

**Solution**:
- Upgrade PHP to 8.2 or higher
- Check your system PHP version:
  ```bash
  php --version
  ```

### Laravel Version Incompatibility

**Problem**: Package requires Laravel 11+, you have Laravel 10

**Solution**:
- This package requires **Laravel 11, 12, or 13**
- For Laravel 10, you'll need to upgrade Laravel:
  ```bash
  composer require laravel/framework:^11.0
  ```

## File Generation Issues

### Files Already Exist

**Problem**: "File already exists" error when running commands

**Solution**:
```bash
# Overwrite existing files with --force flag
php artisan make:repository User --force

# Or delete the existing files manually
rm app/Repositories/UserRepository.php
rm app/Repositories/UserRepositoryImplement.php
php artisan make:repository User
```

### Invalid Directory Structure

**Problem**: Classes generated in wrong location

**Solution**:

Verify your config directories:

```php
// config/service-repository.php
return [
    'repository_directory' => 'app/Repositories',  // ✓ Should exist or be creatable
    'service_directory' => 'app/Services',         // ✓ Should exist or be creatable
];
```

Create directories if they don't exist:
```bash
mkdir -p app/Repositories
mkdir -p app/Services
```

## Namespace Issues

### Class Not Found Exception

**Problem**: "Class not found" when trying to use generated classes

**Solution**:

1. Verify your class exists:
```bash
ls app/Repositories/UserRepository.php
```

2. Check the namespace matches:
```php
// In the file
namespace App\Repositories;  // ✓ Correct

// In your usage
use App\Repositories\UserRepository;  // ✓ Top of file
```

3. Case-sensitive filesystem warning:
   - Linux/Mac filesystems are **case-sensitive**
   - Windows is **case-insensitive**
   - Use PascalCase consistently: `UserRepository`, not `userRepository`

### Custom Namespace

If you changed the namespace in config:

```php
// config/service-repository.php
'repository_namespace' => 'My\Custom\Namespace\Repos',
```

Classes will be generated there:
```php
// Generated class uses custom namespace
namespace My\Custom\Namespace\Repos;

interface UserRepository { ... }
```

And you must use the same namespace:
```php
use My\Custom\Namespace\Repos\UserRepository;
```

## Service Container Issues

### Binding Not Working in Tests

**Problem**: Service container not resolving in test environment

**Solution**:

```php
// In your test
class UserRepositoryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Ensure config is loaded
        $this->app->make('config')->set('service-repository', config('service-repository'));
    }

    public function test_repository_resolves()
    {
        // Should now resolve correctly
        $repo = app(UserRepository::class);
        $this->assertInstanceOf(UserRepositoryImplement::class, $repo);
    }
}
```

### Singleton vs Transient

**Problem**: Same instance expected but getting different instances

**Default behavior**: Classes are **transient** (new instance each time)

**Solution** - Make singleton if needed:

```php
// In your service provider
public function register()
{
    $this->app->singleton(UserRepository::class, UserRepositoryImplement::class);
}
```

## Performance Issues

### Slow Auto-Discovery

**Problem**: Application boots slowly due to class scanning

**Solution**:

Cache the bindings:
```bash
php artisan optimize
```

Or manually cache in production:
```php
// config/service-repository.php
// Consider lazy-loading or caching strategies
```

## Migration Issues

### Upgrading from Older Version

**Problem**: Old configuration format no longer works

**Solution**:

1. Backup old config:
```bash
cp config/service-repository.php config/service-repository.php.backup
```

2. Republish updated config:
```bash
php artisan vendor:publish --tag=repository-with-service-config --force
```

3. Manually migrate any custom settings from backup

## Debugging

### Enable Debug Mode

Check package initialization:

```php
// In tinker or a test
php artisan tinker

// Check if config is published
dd(config('service-repository'));

// Check if classes are bound
dd(app()->bound('App\Repositories\UserRepository'));

// Manually resolve
dd(app('App\Repositories\UserRepository'));
```

### Verbose Command Output

Run artisan commands with verbose flag:

```bash
# See detailed output
php artisan make:repository User -vv
```

### Check Generated Files

Verify generated file contents:

```bash
# View repository interface
cat app/Repositories/UserRepository.php

# View implementation
cat app/Repositories/UserRepositoryImplement.php
```

## Still Having Issues?

1. **Check the logs**:
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. **Run tests**:
   ```bash
   php artisan test
   ```

3. **Check GitHub Issues**:
   - https://github.com/l0n3ly/laravel-repository-with-service/issues

4. **Clear everything**:
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan view:clear
   composer dump-autoload
   ```

## See Also

- [Main Documentation](../README.md) - Installation, quick start, and command reference
- [API Reference](./API.md) - Core interfaces and configuration
- [Examples](./EXAMPLES.md) - Real-world usage examples
