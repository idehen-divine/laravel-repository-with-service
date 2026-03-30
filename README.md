# Laravel Repository With Service

A Laravel package that generates repository and service layers and automatically binds interfaces to implementations.

## Requirements

- PHP ^8.2
- Laravel 11, 12, or 13

## Version Support

| Laravel | Package |
|:-------:|:-------:|
| 11.x    | 1.x     |
| 12.x    | 1.x     |
| 13.x    | 1.x     |

## Installation

Install the latest 1.x release:

```bash
composer require l0n3ly/laravel-repository-with-service"
```

Publish the config file:

```bash
php artisan vendor:publish --provider="L0n3ly\LaravelRepositoryWithService\Providers\PackageProvider" --tag="service-repository-config"
```

## Quick Usage

Generate repository only:

```bash
php artisan make:repository User
php artisan make:repository UserRepository
```

Generate repository with service:

```bash
php artisan make:repository User --service
php artisan make:repository UserRepository --service
```

Generate service only:

```bash
php artisan make:service User
php artisan make:service UserService
php artisan make:service UserService --repository
```

Generate service with blank template:

```bash
php artisan make:service UserService --blank
```

## Override Binding Example

If you want to decorate or replace a bound implementation, you can extend it in your application service provider:

```php
$this->app->extend(Interface::class, function ($service, $app) {
    return new NewImplement($service);
});
```

## Service API Notes

The API service template uses the response helper methods provided by `ResultService`.

Common getters:

```php
$serviceName->getData();
$serviceName->getCode();
$serviceName->getMessage();
$serviceName->getError();
```

Common setters:

```php
$this->setCode();
$this->setData();
$this->setError();
$this->setMessage();
```

## Documentation

Project guide:

- <a href="https://l0n3ly.github.io/laravel-service-repository-pattern-guide/" target="_blank">V1 Docs</a>


## Changelog

See [CHANGELOG](CHANGELOG.md) for release notes.

## License

MIT. See [LICENSE.md](LICENSE.md).
