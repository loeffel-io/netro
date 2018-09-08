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

    /** @var string */
    public $command = "";

    /** @var string */
    public $description = "";

    /** @var string $when after_wp_load|before_wp_load */
    public $when = 'after_wp_load';

    /**
     * Console constructor.
     * @param array $args
     * @param WP_CLI $wpCli
     */
    public function __construct(WP_CLI $wpCli, array $args)
    {
        $this->args = $args;
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
     * @return array
     */
    public function arguments(): array
    {
        return $this->args;
    }
}
