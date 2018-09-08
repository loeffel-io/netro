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
    public $command = 'make:type {name}';

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

    private function createTypeDirectory()
    {
        $this->filesystem->mkdir($this->getTypePath(), 644);
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
     * @return string
     */
    private function createClassContent(string $name, string $filename): string
    {
        $content = file_get_contents($filename);
        $content = str_replace('%class%', ucfirst($name), $content);
        $content = str_replace('%postType%', lcfirst($name), $content);

        return $content;
    }

    /**
     * @param string $name
     */
    private function createClass(string $name)
    {
        $filename = $this->getTypePath() . $name . '.php';
        $distFilename = NETRO_PLUGIN_PATH . 'resources/templates/type/type.php.dist';

        if ($this->fileExists($filename)) {
            return;
        }

        if ($this->fileExists($distFilename) === false) {
            return;
        }

        $this->filesystem->dumpFile($filename, $this->createClassContent($name, $distFilename));
    }

    public function run()
    {
        $name = $this->arguments()[0];

        $this->createTypeDirectory();
        $this->createClass($name);
    }
}
