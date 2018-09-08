<?php

namespace Netro\Console\Command;

use Netro\Console\Console;
use Symfony\Component\Filesystem\Filesystem;
use WP_CLI;

/**
 * Class MakeType
 * @package Netro\Console\Command
 */
class MakeType extends Console
{
    public $command = 'make:type {name} {plural}';

    public $description = 'Make type files';

    /** @var Filesystem */
    private $filesystem;

    /**
     * MakeType constructor.
     * @param WP_CLI $wpCli
     * @param array $args
     * @param array $namedArgs
     * @param Filesystem $filesystem
     */
    public function __construct(WP_CLI $wpCli, array $args, array $namedArgs, Filesystem $filesystem)
    {
        parent::__construct($wpCli, $args, $namedArgs);

        $this->filesystem = $filesystem;
    }

    /**
     * @return string
     */
    private function getTypePath(): string
    {
        return NETRO_TEMPLATE_SOURCE_PATH . 'Type/';
    }

    /**
     * @return string
     */
    private function getFacadePath(): string
    {
        return NETRO_TEMPLATE_SOURCE_PATH . 'Facade/Type/';
    }

    private function createTypeDirectory()
    {
        $this->filesystem->mkdir($this->getTypePath());
    }

    /**
     * @param string $filename
     * @return bool
     */
    private function fileExists(string $filename): bool
    {
        return $this->filesystem->exists($filename);
    }

    /**
     * @param string $name
     * @param string $filename
     * @param string $plural
     * @return string
     */
    private function createContent(string $name, string $filename, string $plural = ""): string
    {
        $content = file_get_contents($filename);
        $content = str_replace('%class%', ucfirst($name), $content);
        $content = str_replace('%postType%', lcfirst($name), $content);

        if (empty($plural) === false) {
            $content = str_replace('%plural%', ucfirst($plural), $content);
        }

        return $content;
    }

    /**
     * @param string $name
     */
    private function createClass(string $name)
    {
        $filename = $this->getTypePath() . ucfirst($name) . '.php';
        $distFilename = NETRO_PLUGIN_PATH . 'resources/templates/type/type.php.dist';

        if ($this->fileExists($filename)) {
            return;
        }

        if ($this->fileExists($distFilename) === false) {
            return;
        }

        $this->filesystem->dumpFile($filename, $this->createContent($name, $distFilename));
    }

    /**
     * @param string $name
     */
    private function createFacade(string $name)
    {
        $filename = $this->getFacadePath() . ucfirst($name) . '.php';
        $distFilename = NETRO_PLUGIN_PATH . 'resources/templates/type/facade.php.dist';

        if ($this->fileExists($filename)) {
            return;
        }

        if ($this->fileExists($distFilename) === false) {
            return;
        }

        $this->filesystem->dumpFile($filename, $this->createContent($name, $distFilename));
    }

    /**
     * @param string $name
     * @param string $plural
     */
    private function createConfig(string $name, string $plural)
    {
        $filename = $this->getTypePath() . ucfirst($name) . '.yml';
        $distFilename = NETRO_PLUGIN_PATH . 'resources/templates/type/config.yml.dist';

        if ($this->fileExists($filename)) {
            return;
        }

        if ($this->fileExists($distFilename) === false) {
            return;
        }

        $this->filesystem->dumpFile($filename, $this->createContent($name, $distFilename, $plural));
    }

    public function run()
    {
        $name = $this->arguments()[0];
        $plural = $this->arguments()[1];

        $this->createTypeDirectory();
        $this->createClass($name);
        $this->createFacade($name);
        $this->createConfig($name, $plural);

        $this->success("Type $name created successfully");
    }
}
