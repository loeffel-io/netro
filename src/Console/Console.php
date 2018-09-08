<?php

namespace Netro\Console;

use WP_CLI;
use WP_CLI\ExitException;

/**
 * Class Console
 * @package Netro\Console
 */
abstract class Console
{
    /** @var WP_CLI */
    private $wpCli;

    /** @var array */
    private $args = [];

    /** @var array */
    private $namedArgs = [];

    /** @var string */
    public $command = "";

    /** @var string */
    public $description = "";

    /** @var string $when after_wp_load|before_wp_load */
    public $when = 'after_wp_load';

    /**
     * Console constructor.
     * @param WP_CLI $wpCli
     * @param array $args
     * @param array $namedArgs
     */
    public function __construct(WP_CLI $wpCli, array $args, array $namedArgs)
    {
        $this->args = $args;
        $this->namedArgs = $namedArgs;
        $this->wpCli = $wpCli;
    }

    /**
     * @param string $message
     */
    public function success(string $message)
    {
        $this->wpCli::success($message);
    }

    /**
     * @param string $message
     * @param bool|null $exit
     * @throws ExitException
     */
    public function error(string $message, ? bool $exit)
    {
        $this->wpCli::error($message, $exit);
    }

    /**
     * @param string $message
     */
    public function info(string $message)
    {
        $this->wpCli::line($message);
    }

    /**
     * @param array $errors
     */
    public function errors(array $errors)
    {
        $this->wpCli::error_multi_line($errors);
    }

    /**
     * @return array
     */
    public function arguments(): array
    {
        return $this->args;
    }

    /**
     * @return array
     */
    public function namedArguments(): array
    {
        return $this->namedArgs;
    }

    /**
     * @param string $name
     * @return null|string
     */
    public function argument(string $name): ?string
    {
        if (isset($this->namedArguments()[$name]) === true) {
            return $this->namedArguments()[$name];
        }

        return null;
    }
}
