<?php

namespace Netro\Type;

/**
 * Class Type
 * @package Netro\Type
 */
abstract class Type
{
    /** @var string */
    protected $postType;

    /**
     * @return string
     */
    public function getPostType(): string
    {
        return $this->postType;
    }
}