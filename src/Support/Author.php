<?php

namespace Netro\Support;

/**
 * Class Author
 * @package Netro\Support
 */
class Author
{
    /** @var int */
    protected $id;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Author
     */
    public function setId(int $id): Author
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getDisplayName(): string
    {
        return $this->getMeta('display_name');
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->getMeta('first_name');
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->getMeta('last_name');
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->getMeta('user_email');
    }

    /**
     * @param string $field
     * @return string
     */
    public function getMeta(string $field): string
    {
        return get_the_author_meta($field, $this->getId());
    }
}
