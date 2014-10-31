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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\ClassLoader\ApcClassLoader;

// run not in cli-server
if (PHP_SAPI != 'cli-server') {
    exit('This script can be run from the CLI-server only.');
}

// immediately return the update log or develop
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

// give static or handle request
if (is_file($file = __DIR__.'/../web'.$request->getScriptName())) {
    $response = new Response();
    // caching
    $response
        ->setPublic()
        ->setEtag(md5_file($file))
        ->setExpires((new \DateTime)->setTimestamp(time()+2592000)) // updates interval of 30 days
        ->setLastModified((new \DateTime)->setTimestamp(filemtime($file)))
        ->headers->addCacheControlDirective('must-revalidate', true);

    // response was not modified for this request
    if (!$response->isNotModified($request)) {
        $response->setContent(file_get_contents($file));
    }

    // set content type
    $mimes = [
        'css' => 'text/css',
        'js' => 'text/javascript'
    ];
    if (isset($mimes[($ext = pathinfo($request->getScriptName(), PATHINFO_EXTENSION))])) {
        $response->headers->set('Content-Type', $mimes[$ext]);
    } else {
        $response->headers->set('Content-Type', mime_content_type($file));
    }
} else {
    $response = $kernel->handle($request);
}

$response->send();
$kernel->terminate($request, $response);
