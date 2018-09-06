<?php

namespace Netro\Facade\Type;

/**
 * Class Post
 * @package Netro\Facade\Type
 */
class Post extends Type
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return \Netro\Type\Post::class;
    }
}
