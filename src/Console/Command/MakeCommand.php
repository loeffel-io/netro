<?php

namespace Netro\Console\Command;

use Netro\Console\Console;
use Symfony\Component\Filesystem\Filesystem;
use WP_CLI;

/**
 * Class MakeCommand
 * @package Netro\Console\Command
 */
class MakeCommand extends Console
{
    public $command = 'make:command {name}';

    public $description = 'Make command';

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
    private function getCommandPath(): string
    {
        return NETRO_TEMPLATE_SOURCE_PATH . 'Console/' . 'Command/';
    }

    private function createCommandDirectory()
    {
        $this->filesystem->mkdir($this->getCommandPath());
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
    private function createContent(string $name, string $filename): string
    {
        $content = file_get_contents($filename);
        $content = str_replace('%class%', ucfirst($name), $content);

        return $content;
    }

    /**
     * @param string $name
     */
    private function createCommand(string $name)
    {
        $filename = $this->getCommandPath() . ucfirst($name) . '.php';
        $distFilename = NETRO_PLUGIN_PATH . 'resources/templates/command/command.php.dist';

        if ($this->fileExists($filename)) {
            return;
        }

        if ($this->fileExists($distFilename) === false) {
            return;
        }

        $this->filesystem->dumpFile($filename, $this->createContent($name, $distFilename));
    }

    public function run()
    {
        $name = $this->arguments()[0];

        $this->createCommandDirectory();
        $this->createCommand($name);

        $this->success("Command $name created successfully");
    }
}
