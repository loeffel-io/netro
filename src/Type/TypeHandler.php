<?php

namespace Netro\Type;

use Netro\HandlerInterface;
use WP_Post;
use Symfony\Component\Yaml\Yaml;
use ReflectionClass;
use ReflectionException;
use DI\Container;

/**
 * Class TypeHandler
 * @package Netro\Type
 */
class TypeHandler implements HandlerInterface
{
    /** @var Type $type */
    protected $type;

    /** @var string $path Netro template path */
    protected $path;

    /** @var Container $container */
    protected $container;

    /** @var Yaml $yaml */
    protected $yaml;

    /**
     * TypeHandler constructor.
     * @param Type $type
     * @param string $path
     * @param Container $container
     */
    public function __construct(Type $type, string $path, Container $container)
    {
        $this->type = $type;
        $this->path = $path;
        $this->container = $container;
        $this->yaml = new Yaml();
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
        $this->type->setConfig($this->yaml->parseFile($this->getConfigPath()));
    }

    private function enableThumbnails()
    {
        if (empty($this->type->getConfig()['supports']) === true) {
            return;
        }

        if (in_array('thumbnail', $this->type->getConfig()['supports']) === false) {
            return;
        }

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

        $this->container->call([$this->type, 'updated'], [$this->type->find($id)]);
    }

    /**
     * @param int $id
     */
    private function fireSavedEvent(int $id)
    {
        if (method_exists($this->type, 'saved') === false) {
            return;
        }

        $this->container->call([$this->type, 'saved'], [$this->type->find($id)]);
    }

    private function enableEvents()
    {
        add_action('save_post', function (int $id, WP_Post $post, bool $update) {
            if ($post->post_type !== $this->type->getPostType()) {
                return;
            }

            if ($post->post_status === 'auto-draft') {
                return;
            }

            if ($post->post_date_gmt === $post->post_modified_gmt || $update === false) {
                $this->fireSavedEvent($id);
                return;
            }

            $this->fireUpdatedEvent($id);
        }, 10, 3);
    }

    /**
     * @param array $app
     */
    public function register(array $app)
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
