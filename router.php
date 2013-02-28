<?php
$call_not_found = function () {
	require 'guepard/404.php';
	exit(0);
};

// run not in cli-server
if (PHP_SAPI != 'cli-server') {
	$call_not_found();
}

// Check that the access to the application by the local computer or local network
// Comment this code to open access to the application from the internet
if (!empty($_SERVER['HTTP_CLIENT_IP']) ||
	!empty($_SERVER['HTTP_X_FORWARDED_FOR']) ||
	empty($_SERVER['REMOTE_ADDR']) ||
	( // localhost
		!in_array($_SERVER['REMOTE_ADDR'], array('127.0.0.1', 'fe80::1', '::1')) &&
		(
			( // local network IPv6
				strpos($_SERVER['REMOTE_ADDR'], ':') !== false &&
				strpos($_SERVER['REMOTE_ADDR'], 'fc00::') !== 0
			) ||
			( // local network IPv4
				strpos($_SERVER['REMOTE_ADDR'], ':') === false &&
				($long = ip2long($_SERVER['REMOTE_ADDR'])) === false ||
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

return false;
/*
if (!($path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))) {
	$call_not_found();
}

$path .= $path[strlen($path)-1] == '/' ? 'index.php' : '';

// give static
if (file_exists(__DIR__.$path)) {
	return false;
}


// route if need

#RewriteRule [0-9]+\-[a-z]+\.html		item.php		[QSA,L] # действие над элементом
#RewriteRule [0-9]+\.html				item.php		[QSA,L] # просмотр элемента
#RewriteRule item\-[a-z]+\.html			item.php		[QSA,L] # действие над новым элементом
if (preg_match('/^\/(?:\d+(?:\-[a-z]+)?)|(?:item\-[a-z]+)\.html$/', $path)) {
	return require 'item.php';
}

$call_not_found();
*/