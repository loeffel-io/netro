<?php

namespace Netro\Facade\Type;

/**
 * Class Page
 * @package Netro\Facade\Type
 */
class Page extends Type
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return \Netro\Type\Page::class;
    }
}
