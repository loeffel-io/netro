<?php

namespace Netro\Support;

use JsonSerializable;

/**
 * Class Image
 * @package Netro\Support
 */
class Image implements JsonSerializable
{
    /** @var int */
    protected $id;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     * @return Image
     */
    public function setId(?int $id): Image
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @param null|string $size
     * @return null|string
     */
    public function getPath(? string $size = 'full'): ?string
    {
        return wp_get_attachment_image_src($this->getId(), $size, false)[0] ?? null;
    }

    /**
     * @return array|null
     */
    public function getMeta(): ?array
    {
        return wp_get_attachment_metadata($this->getId(), false) ?? null;
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
