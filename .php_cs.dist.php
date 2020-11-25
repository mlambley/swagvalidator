<?php
$finder = \PhpCsFixer\Finder::create()
    //Search for files in the current directory
    ->in([__DIR__])
    //Exclude the vendor folder, and any folder beginning with a dot
    ->exclude(['vendor', '.*']);

$config = (new \PhpCsFixer\Config('mlambley'))
    //Risky rules include things like changing != to !== which would be disastrous
    ->setRiskyAllowed(false)
    //On windows machines, setting line endings to \r\n assumes that git.config.autocrlf = true
    //If git preserves \n on checkout, then this should be changed to \n
    ->setLineEnding(PHP_EOL)
    ->setRules([
        //Includes PSR1
        '@PSR2' => true,
        'no_blank_lines_before_namespace' => true,
    ])
    //The cache file should be gitignored
    ->setCacheFile('.php_cs.cache')
    ->setFinder($finder)
    ->setFormat('txt')
    ->setHideProgress(false)
    ->setIndent('    ')
    ->setPhpExecutable(null)
    ->setUsingCache(true)
;

return $config;
