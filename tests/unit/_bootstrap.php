<?php
include __DIR__.'/../../vendor/autoload.php';

$kernel = \AspectMock\Kernel::getInstance();
$kernel->init([
    'debug' => true,
    'cacheDir'  => __DIR__ . '/../_tmp',
    'includePaths' => [__DIR__.'/../../src']
]);
