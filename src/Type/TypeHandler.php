<?php

namespace Netro\Type;

use Symfony\Component\Yaml\Yaml;
use ReflectionClass;
use ReflectionException;
use WP_Post;

/**
 * Class TypeHandler
 * @package Netro\Type
 */
class TypeHandler
{
    /** @var Type $type */
    protected $type;

    /** @var string $path Netro template path */
    protected $path;

    /**
     * TypeHandler constructor.
     * @param Type $type
     * @param string $path
     */
    public function __construct(Type $type, string $path)
    {
        $this->type = $type;
        $this->path = $path;
    }

    /**
     * @return string
     */
    private function getConfigPath(): string
    {
        try {
            $classShortName = (new ReflectionClass($this->type))->getShortName();
            return $this->path . 'type/' . $classShortName . '.yml';
        } catch (ReflectionException $exception) {
            trigger_error($exception->getMessage(), E_USER_ERROR);
            exit(1);
        }
    }

    private function getConfig()
    {
        $this->type->setConfig(Yaml::parseFile($this->getConfigPath()));
    }

    private function enableThumbnails()
    {
        add_theme_support('post-thumbnails');
    }

    /**
     * @param int $id
     */
    private function fireUpdatedEvent(int $id)
    {
        if (method_exists($this->type, 'updated') === false) {
            return;
        }

        call_user_func([$this->type, 'updated'], $this->type->find($id));
    }

    /**
     * @param int $id
     */
    private function fireSavedEvent(int $id)
    {
        if (method_exists($this->type, 'saved') === false) {
            return;
        }

        call_user_func([$this->type, 'saved'], $this->type->find($id));
    }

    private function enableEvents()
    {
        add_action('save_post', function (int $id, WP_Post $post, bool $update) {
            $update ? $this->fireUpdatedEvent($id) : $this->fireSavedEvent($id);
            unset($post);
        }, 10, 3);
    }

    public function register()
    {
        if ($this->type->isRegister() === false) {
            return;
        }

        $this->getConfig();

        add_action('init', function () {
            register_post_type($this->type->getPostType(), $this->type->getConfig());
        });

        $this->enableThumbnails();
        $this->enableEvents();
    }
}