<?php

namespace Netro\Support;

use Netro\Type\Type;

/**
 * Class Image
 * @package Netro\Support
 */
class Image
{
    /** @var int */
    protected $id;

    /** @var Type $type */
    protected $type;

    /**
     * Image constructor.
     * @param Type $type
     */
    public function __construct(Type $type)
    {
        $this->type = $type;
        $this->id = $this->getId();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        if ($this->hasImage() === false) {
            return 0;
        }

        return get_post_thumbnail_id($this->type->getId());
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return bool
     */
    public function hasImage(): bool
    {
        return has_post_thumbnail($this->type->getId());
    }

    /**
     * @param null|string $size
     * @return string
     */
    public function getPath(? string $size = 'thumbnail'): string
    {
        if (!$id = $this->getId()) {
            return "";
        }

        $image = wp_get_attachment_image_src($id, $size, false);

        return $image[0] ?? "";
    }
}