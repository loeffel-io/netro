<?php

namespace Netro\Type;

use Carbon\Carbon;
use Netro\Support\Author;
use Netro\Support\Image;
use WP_Post;
use WP_Query;
use Exception;
use JsonSerializable;

/**
 * Class Type
 * @package Netro\Type
 */
abstract class Type implements JsonSerializable
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

    /** @var Carbon */
    protected $createdAt;

    /** @var Carbon */
    protected $modifiedAt;

    /** @var Image */
    protected $image;

    /** @var Author */
    protected $author;

    /** @var bool */
    protected $register = true;

    /** @var array */
    private $config = [];

    /** @var array */
    private $builder = [];

    /**
     * @param WP_Post $post
     * @return Type
     */
    private function new(WP_Post $post): Type
    {
        /** @var Type $type */
        $class = get_called_class();
        $type = new $class;
        $carbon = new Carbon();

        // Image
        $image = new Image();
        $image->setId((int)get_post_thumbnail_id($post->ID));

        // Author
        $author = new Author();
        $author->setId($post->post_author);

        return $type->setId($post->ID)
            ->setPostType($post->post_type)
            ->setTitle($post->post_title)
            ->setStatus($post->post_status)
            ->setImage($image)
            ->setAuthor($author)
            ->setCreatedAt($carbon::parse($post->post_date_gmt))
            ->setModifiedAt($carbon::parse($post->post_modified_gmt))
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
     * @return Carbon
     */
    public function getCreatedAt(): Carbon
    {
        return $this->createdAt;
    }

    /**
     * @param Carbon $createdAt
     * @return Type
     */
    public function setCreatedAt(Carbon $createdAt): Type
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Carbon
     */
    public function getModifiedAt(): Carbon
    {
        return $this->modifiedAt;
    }

    /**
     * @param Carbon $modifiedAt
     * @return Type
     */
    public function setModifiedAt(Carbon $modifiedAt): Type
    {
        $this->modifiedAt = $modifiedAt;

        return $this;
    }

    /**
     * @return Image|null
     */
    public function getImage(): ?Image
    {
        return $this->image;
    }

    /**
     * @param Image $image
     * @return Type
     */
    public function setImage(Image $image): Type
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Author
     */
    public function getAuthor(): Author
    {
        return $this->author;
    }

    /**
     * @param Author $author
     * @return Type
     */
    public function setAuthor(Author $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @param array $posts
     * @return array
     */
    private function mapPosts(array $posts): array
    {
        return array_map(function (WP_Post $post) {
            return $this->new($post);
        }, $posts);
    }

    /**
     * @param bool $update
     * @return array
     */
    private function getPostArray(bool $update): array
    {
        $array = [
            'post_type' => $this->getPostType(),
            'post_title' => $this->getTitle(),
            'post_content' => $this->getContent() ?? "",
            'post_status' => $this->getStatus(),
            'post_author' => $this->getAuthor()->getId(),
        ];

        if ($update === true) {
            $array['ID'] = $this->getId();
        }

        return $array;
    }

    /**
     * @return bool
     */
    private function handleImage(): bool
    {
        if ($imageId = $this->getImage()->getId()) {
            return (bool)set_post_thumbnail($this->getId(), $imageId);
        };

        return delete_post_thumbnail($this->getId());
    }

    /**
     * @return Type
     * @throws Exception
     */
    public function update(): Type
    {
        $res = wp_update_post($this->getPostArray(true), true);

        if (is_wp_error($res)) {
            throw new Exception($res->get_error_message());
        }

        // Update image
        $this->handleImage();

        return $this->find($this->getId());
    }

    /**
     * @return Type
     * @throws Exception
     */
    public function save(): Type
    {
        $res = wp_insert_post($this->getPostArray(false), true);

        if (is_wp_error($res)) {
            throw new Exception($res->get_error_message());
        }

        // Update image
        $this->handleImage();

        return $this->find($res);
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

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'postType' => $this->getPostType(),
            'title' => $this->getTitle(),
            'content' => esc_html($this->getContent()),
            'createdAt' => $this->getCreatedAt(),
            'modifiedAt' => $this->getModifiedAt(),
            'image' => $this->getImage(),
        ];
    }

    /**
     * @return string
     */
    public function toJson(): string
    {
        return json_encode($this->jsonSerialize());
    }

    /**
     * @return array
     */
    public function get(): array
    {
        $this->builder['post_type'] = $this->getPostType();
        $query = (new WP_Query($this->builder));

        return $this->mapPosts($query->posts);
    }

    /**
     * @param string $status
     * @return Type
     */
    public function whereStatus(string $status): Type
    {
        if (empty(get_post_stati()[$status]) === true) {
            trigger_error('Invalid status', E_USER_ERROR);
        }

        $this->builder['post_status'] = $status;

        return $this;
    }

    /**
     * @param int $limit
     * @return Type
     */
    public function limit(int $limit): Type
    {
        $this->builder['posts_per_page'] = $limit;
        $this->builder['paged'] = 1;

        return $this;
    }

    /**
     * @param int $limit
     * @return array
     */
    public function paginate(int $limit): array
    {
        $this->builder['posts_per_page'] = $limit;
        $this->builder['paged'] = get_query_var('paged') ? get_query_var('paged') : 1;

        return $this->get();
    }

    /**
     * @param string $title
     * @return Type
     */
    public function whereTitle(string $title): Type
    {
        $this->builder['title'] = $title;

        return $this;
    }

    /**
     * @param string $name
     * @param null|string $order
     * @return Type
     */
    public function orderBy(string $name, ? string $order = 'DESC'): Type
    {
        $this->builder['orderby'] = $name;
        $this->builder['order'] = $order;

        return $this;
    }

    /**
     * @param array $args
     * @return Type
     */
    public function raw(array $args): Type
    {
        $this->builder = array_merge($this->builder, $args);

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

        return $this->mapPosts($query->posts);
    }

    /**
     * @return array
     */
    public function all(): array
    {
        $query = (new WP_Query([
            'post_type' => $this->getPostType(),
            'posts_per_page' => -1,
            'post_status' => get_post_stati(),
        ]));

        return $this->mapPosts($query->posts);
    }
}
