<?php

namespace Netro\Type;

/**
 * Class TypeHandler
 * @package Netro\Type
 */
class TypeHandler
{
    /** @var Type $type */
    protected $type;

    /**
     * TypeHandler constructor.
     * @param Type $type
     */
    public function __construct(Type $type)
    {
        $this->type = $type;
    }

    public function register()
    {
        if ($this->type->isRegister() === false) {
            return;
        }

        add_action('init', function () {
            register_post_type($this->type->getPostType(), $this->type->getConfig());
        });
    }
}