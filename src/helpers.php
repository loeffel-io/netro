<?php

use Carbon\Carbon;

if (!function_exists('asset')) {
    /**
     * @param string $path
     * @return string
     */
    function asset(string $path): string
    {
        return get_template_directory_uri() . '/assets/' . $path;
    }
}

if (!function_exists('env')) {
    /**
     * @param string $name
     * @param string|null $default
     * @return string
     */
    function env(string $name, string $default = null): string
    {
        return getenv($name) ? getenv($name) : $default;
    }
}

if (!function_exists('now')) {
    /**
     * @return Carbon
     */
    function now(): Carbon
    {
        return Carbon::now();
    }
}
