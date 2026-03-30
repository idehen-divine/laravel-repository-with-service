# Laravel Repository With Service

[![Latest Version](https://img.shields.io/packagist/v/l0n3ly/laravel-repository-with-service.svg?style=flat-square)](https://packagist.org/packages/l0n3ly/laravel-repository-with-service)
[![Total Downloads](https://img.shields.io/packagist/dt/l0n3ly/laravel-repository-with-service.svg?style=flat-square)](https://packagist.org/packages/l0n3ly/laravel-repository-with-service)
[![License](https://img.shields.io/packagist/l/l0n3ly/laravel-repository-with-service.svg?style=flat-square)](https://packagist.org/packages/l0n3ly/laravel-repository-with-service)

A Laravel package that scaffolds the repository and service pattern — generates repository and service classes with interfaces, and automatically binds them to their implementations via the container.

## ✨ Features

- 🏗️ **Artisan Scaffolding** - Generate repositories and services with `make:repository` and `make:service`
- 🔗 **Auto Binding** - Automatically binds interfaces to implementations via the service container
- 📁 **Subdirectory Support** - Organize classes in nested directories (e.g., `Admin/UserRepository`)
- 🔄 **Paired Generation** - Generate a repository and service together with a single command
- 📋 **Interface-First** - Always generates a contract interface alongside each implementation
- 🎨 **Multiple Templates** - Choose between API service template or blank template

## 📋 Requirements

- PHP ^8.2
- Laravel 11, 12, or 13

## 📦 Installation

Install the latest 1.x release:

```bash
composer require l0n3ly/laravel-repository-with-service
```

Publish the config file:

```bash
php artisan vendor:publish --tag=repository-with-service-config
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
