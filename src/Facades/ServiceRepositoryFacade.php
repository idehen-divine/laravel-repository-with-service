<?php

namespace L0n3ly\LaravelRepositoryWithService\Facades;

use Illuminate\Support\Facades\Facade;
use L0n3ly\LaravelRepositoryWithService\Core\ServiceRepository;

/**
 * @see ServiceRepository
 */
class ServiceRepositoryFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-repository-with-service';
    }
}
