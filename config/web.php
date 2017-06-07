<?php

$configWeb = [
    'id' => 'ImgCache',
    'components' => [
        'router' => function() {
            return new \Strider2038\ImgCache\Service\Router();
        }
    ],
];

$configWebLocalFilename = __DIR__ . '/web-local.php';
$configWebLocal = file_exists($configWebLocalFilename) ? require 'web-local.php' : [];

return array_replace_recursive($configWeb, $configWebLocal);