<?php
/**
 * AnimeDB package
 *
 * @package   AnimeDB
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


// get request
require_once __DIR__.'/bootstrap.php.cache';
$request = Request::createFromGlobals();

// Check that the access to the application by the local computer or local network
// Comment this code to open access to the application from the internet
if ($request->server->get('HTTP_CLIENT_IP') ||
	$request->server->get('HTTP_X_FORWARDED_FOR') ||
	!($addr = $request->server->get('REMOTE_ADDR')) ||
	( // localhost
		!in_array($addr, array('127.0.0.1', 'fe80::1', '::1')) &&
		(
			( // local network IPv6
				($ipv6 = (strpos($addr, ':') !== false)) &&
				strpos($addr, 'fc00::') !== 0
			) ||
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

// give static or run for dev
if (is_file(__DIR__.'/../web'.parse_url($request->getRequestUri(), PHP_URL_PATH)) || $request->getScriptName() == '/app_dev.php') {
	return false;
}


// Use APC for autoloading to improve performance
// Change 'sf2' by the prefix you want in order to prevent key conflict with another application
/*
$loader = new ApcClassLoader('sf2', $loader);
$loader->register(true);
*/

require_once __DIR__.'/AppKernel.php';
require_once __DIR__.'/AppCache.php';

$kernel = new AppKernel('prod', false);
$kernel->loadClassCache();
$kernel = new AppCache($kernel);
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);