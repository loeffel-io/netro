<?php
/*
Plugin Name: Netro
Plugin URI: https://netro.io
Description: The Framework for Wordpress Developers
Version: 0.0.1
Author: Lucas Löffel
Author URI: https://loeffel.io
License: MIT
*/

use Dotenv\Dotenv;
use Composer\Autoload\ClassLoader;
use DI\Container;
use Netro\Type\TypeHandler;
use Netro\Console\ConsoleHandler;

defined('ABSPATH') or die();

// Important path definitions
define('NETRO_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('NETRO_TEMPLATE_PATH', get_template_directory());
define('NETRO_TEMPLATE_SOURCE_PATH', NETRO_TEMPLATE_PATH . '/netro/');
define('NETRO_ENV_PATH', NETRO_TEMPLATE_SOURCE_PATH . '.env');
define('NETRO_APP_PATH', NETRO_TEMPLATE_SOURCE_PATH . 'app.php');

// Setup autoloader
require_once __DIR__ . '/vendor/autoload.php';

$loader = new ClassLoader();
$loader->register();
$loader->addPsr4('Netro\\', [
    NETRO_TEMPLATE_SOURCE_PATH,
    NETRO_PLUGIN_PATH . 'src/',
]);

// Setup container
$container = new Container();

// Load the env config file
if (file_exists(NETRO_ENV_PATH)) {
    $dotenv = new Dotenv(NETRO_TEMPLATE_SOURCE_PATH);
    $dotenv->overload();
}

// Load app file
$app = require_once(NETRO_APP_PATH);

// Autowire commands
$consoleHandler = new ConsoleHandler($container, new WP_CLI());
$consoleHandler->register($app);

// Autowire types
foreach (glob(NETRO_TEMPLATE_SOURCE_PATH . 'type/*.php') as $file) {
    $class = '\\Netro\\Type\\' . basename($file, '.php');
    $typeHandler = new TypeHandler(new $class, NETRO_TEMPLATE_SOURCE_PATH, $container);
    $typeHandler->register($app);
}