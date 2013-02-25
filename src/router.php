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

#    RewriteRule type.html					type.php		[QSA,L] # выбор типа
#    RewriteRule type\-([a-z]+)\.html		type.php?t=$1	[QSA,L] # просмотр конкретного типа
#    RewriteRule genre.html					genre.php		[QSA,L] # выбор жанра
#    RewriteRule genre\-([a-z]+)\.html		genre.php?g=$1	[QSA,L] # просмотр конкретного жанра


$call_not_found();