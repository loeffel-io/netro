<?php

namespace Netro\Support;

use Netro\Type\Type;
use JsonSerializable;

/**
 * Class Image
 * @package Netro\Support
 */
class Image implements JsonSerializable
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
    public function update(int $id): void
    {
        $this->id = $id;

        if (!$id) {
            delete_post_thumbnail($this->type->getId());
            return;
        }

        set_post_thumbnail($this->type->getId(), $id);
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
    public function getPath(? string $size = 'full'): string
    {
        if (!$id = $this->getId()) {
            return "";
        }

        $image = wp_get_attachment_image_src($id, $size, false);

        return $image[0] ?? "";
    }

    /**
     * @return array
     */
    public function getMeta(): array
    {
        if (!$id = $this->getId()) {
            return [];
        }

        return wp_get_attachment_metadata($this->getId(), false);
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'path' => $this->getPath(),
            'meta' => $this->getMeta(),
        ];
    }

    /**
     * @return string
     */
    public function toJson(): string
    {
        return json_encode($this->jsonSerialize());
    }
}
