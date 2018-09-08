<?php

namespace Netro\Console;

use DI\Container;
use Netro\Console\Command\MakeType;
use Netro\HandlerInterface;
use WP_CLI;
use Exception;
use ReflectionClass;
use ReflectionException;
use Closure;

/**
 * Class ConsoleHandler
 * @package Netro\Console
 */
class ConsoleHandler implements HandlerInterface
{
    /** @var array */
    protected $commands = [
        MakeType::class,
    ];

    /** @var Container */
    private $container;

    /** @var WP_CLI */
    private $wpCli;

    /**
     * ConsoleHandler constructor.
     * @param Container $container
     * @param WP_CLI $wpCli
     */
    public function __construct(Container $container, WP_CLI $wpCli)
    {
        $this->container = $container;
        $this->wpCli = $wpCli;
    }

    /**
     * @param string $class
     * @return array
     * @throws ReflectionException
     */
    private function getDefaultProperties(string $class): array
    {
        $reflectionClass = new ReflectionClass($class);

        return $reflectionClass->getDefaultProperties();
    }

    /**
     * @param string $command
     * @return array
     */
    private function parseCommand(string $command): array
    {
        preg_match("/([a-zA-Z\:\_\-\=\?]+)+/", $command, $matches);

        return $matches;
    }

    /**
     * @param string $class
     * @return Closure
     */
    private function commandCallback(string $class): Closure
    {
        return function (array $args) use ($class) {
            $this->container->call([$this->container->make($class, ['args' => $args]), 'run']);
        };
    }

    /**
     * @param string $name
     * @param string $class
     * @param array $options
     * @throws Exception
     */
    private function addCommand(string $name, string $class, array $options = [])
    {
        $this->wpCli::add_command($name, $this->commandCallback($class), $options);
    }

    /**
     * @param array $matches
     * @return string
     */
    private function getCommandName(array $matches): string
    {
        return $matches[0];
    }

    /**
     * @param array $matches
     * @return array
     */
    private function getCommandSynopsis(array $matches): array
    {
        if (empty($matches) === true || count($matches) === 1) {
            return [];
        }

        return [];
    }

    /**
     * @param array $defaultProperties
     * @param array $commandMatches
     * @return array
     */
    private function getOptions(array $defaultProperties, array $commandMatches): array
    {
        return [
            'shortdesc' => $defaultProperties['description'],
            'when' => $defaultProperties['when'],
            'synopsis' => $this->getCommandSynopsis($commandMatches),
        ];
    }

    public function register()
    {
        foreach ($this->commands as $class) {
            try {
                $defaultProperties = $this->getDefaultProperties($class);
                $commandMatches = $this->parseCommand($defaultProperties['command']);
                $options = $this->getOptions($defaultProperties, $commandMatches);

                $this->addCommand($this->getCommandName($commandMatches), $class, $options);
            } catch (Exception $exception) {
                trigger_error($exception->getMessage(), E_USER_ERROR);
            }
        }
    }
}
