<?php

namespace Netro\Console\Command;

use Netro\Console\Console;

/**
 * Class MakeType
 * @package Netro\Console\Command
 */
class MakeType extends Console
{
    public $command = 'make:type {name}';

    public $description = 'Make type files';

    public function run()
    {
        echo 'make bae';
    }
}
