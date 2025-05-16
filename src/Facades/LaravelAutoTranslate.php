<?php

namespace Zdearo\LaravelAutoTranslate\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Zdearo\LaravelAutoTranslate\LaravelAutoTranslate
 */
class LaravelAutoTranslate extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Zdearo\LaravelAutoTranslate\LaravelAutoTranslate::class;
    }
}
