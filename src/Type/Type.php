<?php

namespace Netro\Type;

use WP_Post;
use WP_Query;
use Exception;

/**
 * Class Type
 * @package Netro\Type
 */
abstract class Type implements TypeInterface
{
    /** @var int */
    protected $id;

    /** @var string */
    protected $postType;

    /** @var string */
    protected $title;

    /** @var string */
    protected $content;

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
        $type->setId($post->ID);
        $type->setPostType($post->post_type);
        $type->setTitle($post->post_title);
        $type->setContent(apply_filters('the_content', $post->post_content));

        return $type;
    }

    /**
     * @return int
     */
    public function getId(): int
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
     * @return string
     */
    public function getTitle(): string
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
     * @return string
     */
    public function getContent(): string
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
     */
    protected function setRegister(bool $register): void
    {
        $this->register = $register;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * @param array $config
     */
    public function setConfig(array $config): void
    {
        $this->config = $config;
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
     * @return Type
     * @throws Exception
     */
    public function update(): Type
    {
        $update = wp_update_post([
            'ID' => $this->getId(),
            'post_title' => $this->getTitle(),
            'post_content' => $this->getContent(),
        ], true);

        if (is_wp_error($update)) {
            throw new Exception($update->get_error_message());
        }

        return $this;
    }
}
