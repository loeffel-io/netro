<?php

use Sami\Sami;
use Symfony\Component\Finder\Finder;

$iterator = Finder::create()
    ->files()
    ->in('./src');

return new Sami($iterator, [
    'build_dir' => __DIR__ . '/docs/build',
    'cache_dir' => __DIR__ . '/docs/cache',
]);
