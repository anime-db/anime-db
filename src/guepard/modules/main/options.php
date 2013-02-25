<?php
$options = array(
	// язык сайта
	'lang_id'	=> 'ru',
	// кодировка страницы
	'lang_charset'	=> 'utf-8',
	// время жизни сессии
	'session_life_time'	=> 3600,
	// отображение PHP ошибок
	'php_debug'	=> true,
	// вывод HTTP заголовков
//	'http_headers'	=> true,
	// время кэширования страниц на уровне HTTP
	'http_cache'	=> 3600, // 0 - отключено
	// GZip компрессия страниц
	'compression'	=> false, // как-то криво работает
	// тип переадресации
//	'redirect_metod'	=> 'location', // refresh | html | location
	// домен кук
	'cookie_domain'	=> '',
	// шаблон сайта
	'template'	=> 'homedb',
);