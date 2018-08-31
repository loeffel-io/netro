<?php

namespace Netro\Type;

use WP_Post;
use WP_Query;

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
     */
    public function setId(int $id): void
    {
        $this->id = $id;
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
     */
    public function setPostType(string $postType): void
    {
        $this->postType = $postType;
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
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
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
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
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
}