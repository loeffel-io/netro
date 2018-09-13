<?php

namespace Netro\Facade\Type;

use Netro\Facade\Facade;

/**
 * Class Type
 * @package Netro\Facade
 * @method static \Netro\Type\Type find(int $id)
 * @method static \Netro\Type\Type whereTitle(string $title)
 * @method static array latest(int $limit, string $column = 'id')
 */
abstract class Type extends Facade
{
}
