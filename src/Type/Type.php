<?php

namespace Netro\Type;

use Netro\Support\Image;
use WP_Post;
use WP_Query;
use Exception;

/**
 * Class Type
 * @package Netro\Type
 */
abstract class Type
{
    /** @var int */
    protected $id;

    /** @var string */
    protected $postType;

    /** @var string */
    protected $title;

    /** @var string */
    protected $content;

    /** @var string */
    protected $status;

    /** @var Image */
    protected $image;

    /** @var bool */
    protected $register = true;

    /** @var array */
    private $config = [];

    /**
     * @param WP_Post $post
     * @return Type
     */
    private function new(WP_Post $post): Type
    {
        $class = get_called_class();

        /** @var Type $type */
        $type = new $class;

        return $type->setId($post->ID)
            ->setPostType($post->post_type)
            ->initImage()
            ->setTitle($post->post_title)
            ->setStatus($post->post_status)
            ->setContent(apply_filters('the_content', $post->post_content));
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Type
     */
    protected function setId(int $id): Type
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getPostType(): string
    {
        return $this->postType;
    }

    /**
     * @param string $postType
     * @return Type
     */
    public function setPostType(string $postType): Type
    {
        $this->postType = $postType;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return Type
     */
    public function setTitle(string $title): Type
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @param string $content
     * @return Type
     */
    public function setContent(string $content): Type
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return bool
     */
    public function isRegister(): bool
    {
        return $this->register;
    }

    /**
     * @param bool $register
     * @return Type
     */
    protected function setRegister(bool $register): Type
    {
        $this->register = $register;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getConfig(): ?array
    {
        return $this->config;
    }

    /**
     * @param array $config
     * @return Type
     */
    public function setConfig(array $config): Type
    {
        $this->config = $config;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return Type
     */
    public function setStatus(string $status): Type
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Image|null
     */
    public function getImage(): ?Image
    {
        $this->initImage();

        return $this->image;
    }

    /**
     * @param int $thumbnailId
     * @return Type
     */
    public function updateImage(int $thumbnailId): Type
    {
        $this->initImage();
        $this->image->update($thumbnailId);

        return $this;
    }

    /**
     * @return Type
     */
    public function initImage(): Type
    {
        if (empty($this->id)) {
            trigger_error("Type id is missing", E_USER_ERROR);
        }

        if (!$this->image instanceof Image) {
            $this->image = new Image($this);
        }

        return $this;
    }

    /**
     * @param int $id
     * @return Type
     */
    public function find(int $id): Type
    {
        $query = (new WP_Query([
            'post_type' => $this->getPostType(),
            'p' => $id,
        ]));

        return $this->new($query->post);
    }

    /**
     * @param array $ids
     * @return array
     */
    public function findMany(array $ids): array
    {
        $query = (new WP_Query([
            'post_type' => $this->getPostType(),
            'post__in' => $ids,
        ]));

        return array_map(function ($post) {
            return $this->new($post);
        }, $query->posts);
    }

    /**
     * @return array
     */
    public function all(): array
    {
        $query = (new WP_Query([
            'post_type' => $this->getPostType(),
        ]));

        return array_map(function ($post) {
            return $this->new($post);
        }, $query->posts);
    }

    /**
     * @param bool $update
     * @return array
     */
    private function getPostArray(? bool $update = false): array
    {
        $array = [
            'post_type' => $this->getPostType(),
            'post_title' => $this->getTitle(),
            'post_content' => $this->getContent() ?? "",
            'post_status' => $this->getStatus(),
        ];

        if ($update) {
            $array['ID'] = $this->getId();
        }

        return $array;
    }

    /**
     * @return Type
     * @throws Exception
     */
    public function update(): Type
    {
        $update = wp_update_post($this->getPostArray(true), true);

        if (is_wp_error($update)) {
            throw new Exception($update->get_error_message());
        }

        return $this->find($this->getId());
    }

    /**
     * @return Type
     * @throws Exception
     */
    public function save(): Type
    {
        $save = wp_insert_post($this->getPostArray(), true);

        if (is_wp_error($save)) {
            throw new Exception($save->get_error_message());
        }

        return $this->find($save);
    }

    /**
     * @return bool
     */
    public function delete(): bool
    {
        $delete = wp_delete_post($this->getId());

        if ($delete instanceof WP_Post) {
            return true;
        }

        return false;
    }
}
