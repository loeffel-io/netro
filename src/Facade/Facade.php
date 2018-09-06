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
    /** @var mixed */
    private static $instance;

    /**
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return "";
    }

    /**
     * @return mixed|null
     * @throws DependencyException
     * @throws NotFoundException
     */
    private static function getInstance()
    {
        if (!class_exists(static::getFacadeAccessor())) {
            return null;
        }

        $container = new Container();

        return $container->make(static::getFacadeAccessor());
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
        if (!self::$instance) {
            self::$instance = static::getInstance();
        }

        return self::$instance->$method(...$args);
    }
}
