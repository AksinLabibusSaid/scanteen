<?php

declare(strict_types=1);

/**
 * Minimal PSR-4 style autoload for the App\ namespace.
 */
if (!defined('SCANTEEN_ROOT')) {
    define('SCANTEEN_ROOT', dirname(__DIR__));
}

// Composer autoloader
if (is_file(SCANTEEN_ROOT . '/vendor/autoload.php')) {
    require_once SCANTEEN_ROOT . '/vendor/autoload.php';
}

// Simple .env loader
if (file_exists(SCANTEEN_ROOT . '/.env')) {
    $lines = file(SCANTEEN_ROOT . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        $parts = explode('=', $line, 2);
        if (count($parts) === 2) {
            putenv(trim($parts[0]) . '=' . trim($parts[1]));
        }
    }
}

spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }
    $relative = substr($class, strlen($prefix));
    $path = SCANTEEN_ROOT . '/app/' . str_replace('\\', DIRECTORY_SEPARATOR, $relative) . '.php';
    if (is_file($path)) {
        require $path;
    }
});
