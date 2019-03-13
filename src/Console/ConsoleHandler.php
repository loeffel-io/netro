<?php

namespace Netro\Console;

use DI\Container;
use Netro\Console\Command\MakeCommand;
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
        MakeCommand::class,
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
        preg_match_all("/[a-zA-Z\:\_\-\=\?]+/", $command, $matches);

        return $matches;
    }

    /**
     * @param string $class
     * @return Closure
     */
    private function commandCallback(string $class): Closure
    {
        return function (array $args, array $namedArgs) use ($class) {
            $this->container->call([
                $this->container->make($class, ['args' => $args, 'namedArgs' => $namedArgs]),
                'run'
            ]);
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
        return $matches[0][0];
    }

    /**
     * @param array $optionMatch
     * @return string
     */
    private function parseSynopsisType(array $optionMatch): string
    {
        if (empty($optionMatch[1]) === false && $optionMatch[1] === '--') {
            return 'assoc';
        }

        return 'positional';
    }

    /**
     * @param array $optionMatch
     * @return bool
     */
    private function parseSynopsisOptional(array $optionMatch): bool
    {
        if (empty($optionMatch[3]) === false) {
            return true;
        }

        return false;
    }

    /**
     * @param array $optionMatch
     * @return string
     */
    private function parseSynopsisName(array $optionMatch): string
    {
        if (empty($optionMatch[2]) === false) {
            return $optionMatch[2];
        }

        return '';
    }

    /**
     * @param array $optionMatch
     * @return null|string
     */
    private function parseSynopsisDefault(array $optionMatch): ?string
    {
        if (empty($optionMatch[4]) === false && $optionMatch[4] === '=') {
            return $optionMatch[5] ?? null;
        }

        return null;
    }

    /**
     * @param array $synopsis
     */
    private function parseSynopsisSpecial(array &$synopsis)
    {
        if ($synopsis['type'] !== 'assoc') {
            return;
        }

        if ($synopsis['default'] === null) {
            $synopsis['type'] = 'flag';
            $synopsis['optional'] = true;
            return;
        }

        if (empty($synopsis['default']) === false) {
            $synopsis['optional'] = true;
            return;
        }
    }

    /**
     * @param array $optionMatch
     * @return array
     */
    private function parseSynopsis(array $optionMatch): array
    {
        // --
        $synopsis['type'] = $this->parseSynopsisType($optionMatch);

        // name
        $synopsis['name'] = $this->parseSynopsisName($optionMatch);

        // optional
        $synopsis['optional'] = (string)$this->parseSynopsisOptional($optionMatch);

        // default
        $synopsis['default'] = $this->parseSynopsisDefault($optionMatch);

        // flag (special)
        $this->parseSynopsisSpecial($synopsis);

        return $synopsis;
    }

    /**
     * @param array $matches
     * @return array
     */
    private function getCommandSynopsis(array $matches): array
    {
        $synopsis = [];

        if (empty($matches) === true || count($matches) === 1) {
            return $synopsis;
        }

        foreach ($matches as $index => $match) {
            if ($index === 0) {
                continue;
            }

            if (preg_match("/^([\-]{2})?([a-zA-Z]+)(?:(\?)|([\=]{1})([a-zA-Z]+))?/", $match, $optionMatch) === false) {
                continue;
            }

            $synopsis[] = $this->parseSynopsis($optionMatch);
        }

        return $synopsis;
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
            'synopsis' => $this->getCommandSynopsis($commandMatches[0]),
        ];
    }

    /**
     * @param array $config
     * @return array
     */
    public function mergeConfig(array $config): array
    {
        return array_merge($this->commands, $config);
    }

    /**
     * @param array $app
     */
    public function register(array $app)
    {
        $this->commands = $this->mergeConfig($app['commands']);

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
