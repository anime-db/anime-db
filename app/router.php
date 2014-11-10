<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\ClassLoader\ApcClassLoader;

// run not in cli-server
if (PHP_SAPI != 'cli-server') {
    exit('This script can be run from the CLI-server only.');
}

// immediately return the update log or dev
if ($_SERVER['SCRIPT_NAME'] == '/update.log' || $_SERVER['SCRIPT_NAME'] == '/app_dev.php') {
    return false;
}

// get request
$loader = require_once __DIR__.'/bootstrap.php.cache';
$request = Request::createFromGlobals();

// Use APC for autoloading to improve performance
if (extension_loaded('apc')) {
    $loader = new ApcClassLoader('sf2', $loader);
    $loader->register(true);
}

require_once __DIR__.'/AppKernel.php';
require_once __DIR__.'/AppCache.php';

$kernel = new AppKernel('prod', false);
$kernel->loadClassCache();
$kernel = new AppCache($kernel);

$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
