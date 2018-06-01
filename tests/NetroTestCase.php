<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionProperty;
use ReflectionException;

/**
 * Class NetroTestCase
 * @package Tests
 */
class NetroTestCase extends TestCase
{
    /**
     * @param mixed $class
     * @param string $property
     * @return ReflectionProperty
     * @throws ReflectionException
     */
    protected function getSecretProperty($class, string $property): ReflectionProperty
    {
        $reflection = new ReflectionClass($class);
        $property = $reflection->getProperty($property);
        $property->setAccessible(true);

        return $property;
    }
}
