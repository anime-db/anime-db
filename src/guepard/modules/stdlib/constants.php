<?php

/**
 * Константы пакета
 *
 * @package	Standard Library
 * @author	Peter Gribanov
 * @since	10.05.2011
 */

// путь к сайту
define('stdlib\HOST',
	($_SERVER['SERVER_PROTOCOL'][4]=='S' ? 'https' : 'http')
	. '://'.$_SERVER['HTTP_HOST']);

// корневая директория
define('stdlib\ROOT', $_SERVER['DOCUMENT_ROOT'] . '/');