# Laravel Repository With Service — Quick Guide

This package scaffolds the Repository + Service pattern for Laravel and provides automatic container binding and
generation commands. See the full documentation for details.

## Quick Overview

- Purpose: Generate repositories and services, auto-bind implementations, and standardize data/business layers.
- Location: repositories in `app/Repositories`, services in `app/Services`.
- Naming: `Repository` / `Service` interfaces; `RepositoryImplement` / `ServiceImplement` implementations.

## Common Commands

```bash
php artisan make:model User --all # model + repo + service + migration/factory/seeder
php artisan make:repository Post --service --api
php artisan make:service Order --api
```

## Key Conventions

- Repositories handle data access (`all`, `find`, `create`, `update`, `delete`, `updateOrCreate`, `firstOrCreate`,
`query`).
- Services orchestrate business logic and may use the `ResultService` trait for API responses.
- Prefer type-hinting models and return types; keep repositories focused and services thin.

## Where to Read More

- Note: when this guideline is generated into an application it cannot link back into the package repository.
- Package documentation is shipped with the installed package; check `vendor/l0n3ly/laravel-repository-with-service/docs/` inside your project after installation.
- Alternatively, view the project documentation online: https://github.com/l0n3ly/laravel-repository-with-service

--
Small, focused guidance — refer to the docs for full patterns and samples.
