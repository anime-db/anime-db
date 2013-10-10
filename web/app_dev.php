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

// If you don't want to setup permissions the proper way, just uncomment the following PHP line
// read http://symfony.com/doc/current/book/installation.html#configuration-and-setup for more information
//umask(0000);

$loader = require_once __DIR__.'/../app/bootstrap.php.cache';
require_once __DIR__.'/../app/AppKernel.php';

$kernel = new AppKernel('dev', true);
//$kernel->loadClassCache();
$request = Request::createFromGlobals();

// give static or run for dev
if (is_file(__DIR__.$request->getPathInfo())) {
    $response = new Response(file_get_contents(__DIR__.$request->getPathInfo()));
    $response->headers->set('Content-Type', mime_content_type(__DIR__.$request->getPathInfo()));
} else {
    $response = $kernel->handle($request);
}
$response->send();
$kernel->terminate($request, $response);
