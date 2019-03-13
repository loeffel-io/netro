<?php

use Carbon\Carbon;

if (!function_exists('asset')) {
    /**
     * @param string $path
     * @param bool $dev
     * @return string
     */
    function asset(string $path, bool $dev): string
    {
        $path = get_template_directory_uri() . '/assets/' . $path;

        if ($dev !== true) {
            return $path;
        }

        return $path . '?v=' . rand(1, 9999);
    }
}

if (!function_exists('env')) {
    /**
     * @param string $name
     * @param string|null $default
     * @return null|string
     */
    function env(string $name, string $default = null): ?string
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
        return (new Carbon())->now();
    }
}
