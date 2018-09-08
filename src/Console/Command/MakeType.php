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
     * @param string $name
     */
    private function createClass(string $name)
    {
        $filename = $this->getTypePath() . $name . '.php';
        $distFilename = NETRO_PLUGIN_PATH . 'resources/templates/type/type.php.dist';

        if ($this->filesystem->exists($filename)) {
            return;
        }

        if ($this->filesystem->exists($distFilename) === false) {
            return;
        }

        $content = file_get_contents($distFilename);
        $content = str_replace('%class%', ucfirst($name), $content);
        $content = str_replace('%postType%', lcfirst($name), $content);

        $this->filesystem->dumpFile($filename, $content);
    }

    public function run()
    {
        $name = $this->arguments()[0];

        $this->createTypeDirectory();
        $this->createClass($name);
    }
}
