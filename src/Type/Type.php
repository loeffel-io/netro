<?php

namespace Netro\Type;

use Carbon\Carbon;
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
        /** @var Type $type */
        $class = get_called_class();
        $type = new $class;
        $carbon = new Carbon();

        return $type->setId($post->ID)
            ->setPostType($post->post_type)
            ->initImage()
            ->setTitle($post->post_title)
            ->setStatus($post->post_status)
            ->setCreatedAt($carbon::parse($post->post_date_gmt))
            ->setModifiedAt($carbon::parse($post->post_modified_gmt))
            ->setContent(apply_filters('the_content', $post->post_content));
    }

    /**
     * @param TypeBuilder $typeBuilder
     * @return array
     */
    public function getByBuilder(TypeBuilder $typeBuilder): array
    {
        $query = (new WP_Query($typeBuilder->getBuilder()));

        return $this->mapPosts($query->posts);
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
        ];

        if ($update === true) {
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
        $res = wp_update_post($this->getPostArray(true), true);

        if (is_wp_error($res)) {
            throw new Exception($res->get_error_message());
        }

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
     * @return TypeBuilder
     */
    public function builder(): TypeBuilder
    {
        return new TypeBuilder($this);
    }
}
