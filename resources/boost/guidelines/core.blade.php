<x-guide name="Laravel Repository With Service">

    # Repository + Service Pattern

    This package scaffolds the Repository + Service pattern for Laravel with automatic container binding and code
    generation. Use repositories for data access and services for business logic orchestration.

    ## Quick Overview

    - **Purpose**: Generate repositories and services, auto-bind implementations, standardize data/business layers.
    - **Location**: repositories in `app/Repositories`, services in `app/Services`.
    - **Naming**: `Repository` / `Service` interfaces; `RepositoryImplement` / `ServiceImplement` implementations.

    ## Common Commands

    ```bash
    php artisan make:model User --all
    php artisan make:repository Post --service --api
    php artisan make:service Order --api
    ```

    ## Data Access Methods

    Repositories provide these core methods for data access:

    - `all()` - Return all records
    - `find($id)` - Find by ID
    - `findOrFail($id)` - Find or throw exception
    - `create($data)` - Create new record
    - `update($id, $data)` - Update record (returns Model)
    - `delete($id)` - Delete single record
    - `destroy(array $ids)` - Delete multiple records
    - `query()` - Get fresh query builder
    - `updateOrCreate($where, $values)` - Update or create
    - `firstOrCreate($where, $values)` - Find or create

    ## Service Conventions

    - Inject repositories via constructor
    - Use `ResultService` trait for API responses
    - Wrap operations in try-catch
    - Keep business logic separate from data access
    - Type all parameters and return values

    ## Documentation

    Full documentation available:
    - In package: `vendor/l0n3ly/laravel-repository-with-service/docs/`
    - Online: https://github.com/l0n3ly/laravel-repository-with-service

</x-guide>
