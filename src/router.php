<?php
$call_not_found = function () {
	require 'guepard/404.php';
	exit(0);
};

// run not in cli-server
if (PHP_SAPI != 'cli-server') {
	$call_not_found();
}

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