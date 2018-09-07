<?php

namespace Netro\Facade;

use DI\Container;
use \DI\DependencyException;
use \DI\NotFoundException;
use Netro\Support\Mail;

/**
 * Class Facade
 * @package Netro\Facade
 */
abstract class Facade
{
    /** @var array */
    private static $instances;

    /** @var array */
    private static $reset = [
        Mail::class,
    ];

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
     * @param string $accessor
     * @return bool
     */
    private static function shouldCreateInstance(string $accessor): bool
    {
        if (empty(self::$instances[$accessor]) === true) {
            return true;
        }

        if (in_array($accessor, self::$reset) === true) {
            return true;
        }

        if (preg_match("/^Netro\\Type\\.*$/", $accessor) !== false) {
            return true;
        }

        return false;
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

        if (self::shouldCreateInstance($accessor) === true) {
            self::$instances[$accessor] = static::getInstance();
        }

        return self::$instances[$accessor]->$method(...$args);
    }
}
