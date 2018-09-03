<?php

namespace Netro\Type;

/**
 * Class TypeBuilder
 * @package Netro\Type
 */
class TypeBuilder
{
    /** @var Type */
    private $type;

    /** @var array */
    private $builder;

    /**
     * TypeBuilder constructor.
     * @param Type $type
     */
    public function __construct(Type $type)
    {
        $this->type = $type;
    }

    /**
     * @return array
     */
    public function getBuilder(): array
    {
        return $this->builder;
    }

    /**
     * @return array
     */
    public function get(): array
    {
        $this->builder['post_type'] = $this->type->getPostType();

        return $this->type->getByBuilder($this);
    }

    /**
     * @param string $status
     * @return TypeBuilder
     */
    public function whereStatus(string $status): TypeBuilder
    {
        if (empty(get_post_stati()[$status]) === true) {
            trigger_error('Invalid status', E_USER_ERROR);
        }

        $this->builder['post_status'] = $status;

        return $this;
    }

    /**
     * @param int $limit
     * @return TypeBuilder
     */
    public function limit(int $limit): TypeBuilder
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
     * @return TypeBuilder
     */
    public function whereTitle(string $title): TypeBuilder
    {
        $this->builder['title'] = $title;

        return $this;
    }

    /**
     * @param string $name
     * @param null|string $order
     * @return TypeBuilder
     */
    public function orderBy(string $name, ? string $order = 'DESC'): TypeBuilder
    {
        $this->builder['orderby'] = $name;
        $this->builder['order'] = $order;

        return $this;
    }
}
