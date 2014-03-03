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

// immediately return the update log #88
if ($_SERVER['SCRIPT_NAME'] == '/update.log') {
    return false;
}


// get request
$loader = require_once __DIR__.'/bootstrap.php.cache';
$request = Request::createFromGlobals();

// Check that the access to the application by the local computer or local network
// Comment this code to open access to the application from the internet
if ($request->server->get('HTTP_CLIENT_IP') ||
    $request->server->get('HTTP_X_FORWARDED_FOR') ||
    !($addr = $request->server->get('REMOTE_ADDR')) ||
    ( // localhost
        !in_array($addr, ['127.0.0.1', 'fe80::1', '::1']) &&
        (
            // local network IPv6
            (($ipv6 = (strpos($addr, ':') !== false)) && strpos($addr, 'fc00::') !== 0) ||
            ( // local network IPv4
                !$ipv6 &&
                ($long = ip2long($addr)) === false ||
                !(
                    ($long >= ip2long('10.0.0.0')    && $long <= ip2long('10.255.255.255')) ||
                    ($long >= ip2long('172.16.0.0')  && $long <= ip2long('172.31.255.255')) ||
                    ($long >= ip2long('192.168.0.0') && $long <= ip2long('192.168.255.255'))
                )
            )
        )
    )
) {
    header('HTTP/1.0 403 Forbidden');
    exit('You are not allowed to access this application.');
}

// skip dev
if ($request->getBaseUrl() == '/app_dev.php') {
    return false;
}


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
        ->setExpires(new \DateTime('@'.(time()+2592000))) // updates interval of 30 days
        ->setLastModified(new \DateTime('@'.filemtime($file)))
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

    // compress response
    if (($encoding = $request->headers->get('Accept-Encoding')) && $response->getContent()) {
        if (stripos($encoding, 'gzip') !== false) {
            $response->setContent(gzencode($response->getContent(), 9, FORCE_GZIP));
            $response->headers->set('Content-Encoding', 'gzip');

        } elseif (stripos($encoding, 'deflate') !== false) {
            $response->setContent(gzencode($response->getContent(), 9, FORCE_DEFLATE));
            $response->headers->set('Content-Encoding', 'deflate');
        }
    }
} else {
    $response = $kernel->handle($request);
}

$response->send();
$kernel->terminate($request, $response);