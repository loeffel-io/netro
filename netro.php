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

use \Dotenv\Dotenv;

defined('ABSPATH') or die();

// Important path definitions
define('NETRO_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('NETRO_TEMPLATE_PATH', get_template_directory());
define('NETRO_TEMPLATE_SOURCE_PATH', NETRO_TEMPLATE_PATH . '/netro/');
define('NETRO_ENV_PATH', NETRO_TEMPLATE_PATH . '/.env');

// Setup autoloader
$loader = require_once __DIR__ . '/vendor/autoload.php';
$loader->addPsr4('Netro\\', [
    NETRO_TEMPLATE_SOURCE_PATH,
    NETRO_PLUGIN_PATH . 'src/',
]);

// Load the env config file
if (file_exists(NETRO_ENV_PATH)) {
    (new Dotenv(NETRO_TEMPLATE_PATH))->overload();
}

// Autowire types
foreach (glob(NETRO_TEMPLATE_SOURCE_PATH . 'type/*.php') as $file) {
    $class = '\\Netro\\Type\\' . basename($file, '.php');
    $type = new $class;
}