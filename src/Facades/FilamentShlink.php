<?php

namespace Adereksisusanto\FilamentShlink\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Adereksisusanto\FilamentShlink\FilamentShlink
 */
class FilamentShlink extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Adereksisusanto\FilamentShlink\FilamentShlink::class;
    }
}
