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
umask(0000);

$loader = require_once __DIR__.'/../app/bootstrap.php.cache';
require_once __DIR__.'/../app/AppKernel.php';

$kernel = new AppKernel('dev', true);
$request = Request::createFromGlobals();

// give static or handle request
if (is_file($file = __DIR__.$request->getPathInfo())) {
    $response = new Response(file_get_contents($file));

    // set content type
    $mimes = [
        'css' => 'text/css',
        'js' => 'text/javascript'
    ];
    if (isset($mimes[($ext = pathinfo($request->getPathInfo(), PATHINFO_EXTENSION))])) {
        $response->headers->set('Content-Type', $mimes[$ext]);
    } else {
        $response->headers->set('Content-Type', mime_content_type($file));
    }
} else {
    $response = $kernel->handle($request);
}

$response->send();
$kernel->terminate($request, $response);
