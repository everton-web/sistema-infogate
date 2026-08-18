<?php

$basePath = dirname(__DIR__);

$dirs = [
    $basePath . '/storage',
    $basePath . '/storage/app',
    $basePath . '/storage/framework',
    $basePath . '/storage/framework/cache',
    $basePath . '/storage/framework/sessions',
    $basePath . '/storage/framework/views',
    $basePath . '/storage/logs',
    $basePath . '/bootstrap/cache',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    chmod($dir, 0755);
}

if (file_exists($basePath . '/database/database.sqlite')) {
    chmod($basePath . '/database/database.sqlite', 0664);
    chmod($basePath . '/database', 0755);
}

echo 'Permissions fixed.';

unlink(__FILE__);
