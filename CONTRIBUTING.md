# Contributing

Thank you for considering contributing to Laravel Repository With Service! We welcome all contributions, from bug reports to feature suggestions and pull requests.

## Getting Started

### Fork & Clone

```bash
git clone https://github.com/YOUR-USERNAME/laravel-repository-with-service.git
cd laravel-repository-with-service
```

### Install Dependencies

```bash
composer install
```

### Run Tests

```bash
composer test
# or
php artisan test
```

## Development Workflow

### 1. Create a Branch

Use descriptive branch names:

```bash
git checkout -b feature/add-eloquent-support
git checkout -b fix/binding-issue
git checkout -b docs/update-readme
```

### 2. Make Changes

- Add features or fix bugs
- Write or update tests
- Update documentation

### 3. Run Tests

Ensure all tests pass:

```bash
composer test
```
## Security & Dependencies

This package enforces secure dependency resolution with `composer audit --strict`. The `block-insecure` setting in `composer.json` is set to `true`, which means:

- **Composer will block installation** if any dependency has a known security vulnerability
- **All contributors** must keep dependencies updated and address advisories
- **Users can trust** that this package doesn't introduce known vulnerabilities

When updating dependencies, ensure `composer install` and `composer audit` pass without warnings. If advisories appear:

1. Check if a patched version is available
2. Update the dependency constraint in `composer.json`
3. Run `composer update <package-name>` to resolve
4. Verify tests pass: `composer test`

For more information on Composer security audits, see [Composer Security Advisories](https://packagist.org/advisories).
With coverage report:

```bash
composer test -- --coverage
```

### 4. Code Style

Check code style with Pint:

```bash
./vendor/bin/pint
```

Or auto-fix:

```bash
./vendor/bin/pint --test
```

### 5. Static Analysis

Run Laravel Stan:

```bash
./vendor/bin/phpstan analyse
```

### 6. Commit & Push

```bash
git add .
git commit -m "feat: add descriptive message"
git push origin feature/your-feature
```

## Pull Request Process

1. **Describe your changes** - What does this PR accomplish?
2. **Reference issues** - Link any related issues with `#123`
3. **Include tests** - All new features should have tests
4. **Update docs** - Reflect changes in documentation
5. **Follow conventions** - Match existing code style

### PR Template

```markdown
## Description
Brief description of changes

## Type
- [ ] Bug fix
- [ ] New feature
- [ ] Documentation
- [ ] Refactoring

## Related Issues
Fixes #123

## Testing
- [ ] Unit tests added/updated
- [ ] Manual testing completed

## Checklist
- [ ] Code follows project style
- [ ] Documentation updated
- [ ] Tests pass
- [ ] No breaking changes
```

## Reporting Bugs

Create an issue with:

1. **Title** - Clear, descriptive title
2. **Description** - What's happening?
3. **Steps to reproduce** - How to trigger the bug
4. **Expected behavior** - What should happen?
5. **Screenshots/Logs** - Any helpful output

### Bug Report Template

```markdown
## Bug Description
Brief description...

## Steps to Reproduce
1. Step one
2. Step two
3. Error occurs

## Expected Behavior
Should do this...

## Actual Behavior
Does this instead...

## Environment
- Laravel Version: 11
- PHP Version: 8.2
- Package Version: 1.0
```

## Suggesting Features

Open a discussion or issue with:

1. **Problem** - What pain point does this solve?
2. **Solution** - How should it work?
3. **Examples** - Show how you'd use it
4. **Alternatives** - Any other approaches?

## Coding Standards

### PHP Style

Follow PSR-12 standards:

```php
<?php

namespace L0n3ly\LaravelRepositoryWithService;

class Example
{
    public function method(): string
    {
        return 'value';
    }
}
```

### Type Hints

Always use type hints:

```php
public function store(string $name, int $count): User
{
    // ...
}
```

### Documentation

Add PHPDoc comments:

```php
/**
 * Store a new user
 *
 * @param  string  $name
 * @param  int  $count
 * @return User
 */
public function store(string $name, int $count): User
{
    // ...
}
```

### Tests

Write descriptive tests:

```php
public function test_can_create_user_repository()
{
    $this->artisan('make:repository', ['name' => 'User'])
        ->assertSuccessful();

    $this->assertFileExists(app_path('Repositories/UserRepository.php'));
}
```

## Project Structure

```
src/
├── Commands/          # Artisan commands
├── Contracts/         # Interfaces
├── Core/              # Core logic
├── Facades/           # Facades
├── Helpers/           # Helper classes
├── Implementations/   # Implementations
├── Providers/         # Service providers
├── Services/          # Service classes
└── Traits/            # Reusable traits

tests/
├── Unit/              # Unit tests
└── TestCase.php       # Base test class

docs/
├── INSTALLATION.md    # Installation guide
├── QUICKSTART.md      # Quick start
├── COMMANDS.md        # Command reference
├── API.md             # API documentation
└── TROUBLESHOOTING.md # Help guide
```

## Areas for Contribution

### High Priority

- [ ] Bug fixes and stability improvements
- [ ] Performance optimizations
- [ ] Test coverage increases
- [ ] Documentation improvements

### Medium Priority

- [ ] Feature enhancements
- [ ] Code refactoring
- [ ] Example applications
- [ ] Integration guides

### Low Priority

- [ ] Cosmetic improvements
- [ ] Comment updates
- [ ] Minor typo fixes

## Questions?

- **Discussion**: GitHub Discussions
- **Issues**: GitHub Issues
- **Email**: Contact maintainer

## License

By contributing, you agree that your contributions will be licensed under the same MIT License that covers the project.

---

Thank you for helping make this package better! 🙏
