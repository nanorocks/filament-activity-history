<?php

namespace Nanorocks\FilamentActivityHistory\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Nanorocks\FilamentActivityHistory\FilamentActivityHistory
 */
class FilamentActivityHistory extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Nanorocks\FilamentActivityHistory\FilamentActivityHistory::class;
    }
}
