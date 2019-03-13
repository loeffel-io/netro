<?php

namespace Netro\Facade\Support;

use Netro\Facade\Facade;

/**
 * Class Mail
 * @package Netro\Facade\Support
 */
class Mail extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return \Netro\Support\Mail::class;
    }
}
