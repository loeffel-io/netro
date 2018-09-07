<?php

namespace Netro\Facade;

use DI\Container;
use \DI\DependencyException;
use \DI\NotFoundException;

/**
 * Class Facade
 * @package Netro\Facade
 */
abstract class Facade
{
    /** @var array */
    private static $instances;

    /**
     * @return string
     */
    abstract protected static function getFacadeAccessor(): string;

    /**
     * @return mixed|null
     * @throws DependencyException
     * @throws NotFoundException
     */
    private static function getInstance()
    {
        $accessor = static::getFacadeAccessor();

        if (!class_exists($accessor)) {
            return null;
        }

        $container = new Container();

        return $container->make($accessor);
    }

    /**
     * @param string $method
     * @param array $args
     * @return mixed
     * @throws DependencyException
     * @throws NotFoundException
     */
    public static function __callStatic(string $method, array $args)
    {
        $accessor = static::getFacadeAccessor();

        if (empty(self::$instances[$accessor]) === true) {
            self::$instances[$accessor] = static::getInstance();
        }

        return self::$instances[$accessor]->$method(...$args);
    }
}
