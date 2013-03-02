<?php
/**
 * @license GNU GPL Version 3
 * @copyright 2009, Peter Gribanov
 * @link http://peter-gribanov.ru/license
 * @package	FormManager
 */
$options = array(

	// KCAPTCHA configuration file

	// symbols used to draw CAPTCHA
//	'allowed_symbols' => '0123456789', // digits
	'allowed_symbols' => '23456789ABCDEFGHKLMNPQRSTUVWXYZ', // alphabet without similar symbols (o=0, 1=l, i=j, t=f)
//	'allowed_symbols' => 'MNPQRSTUVWZ', // test

	// CAPTCHA string length
//	'length' => mt_rand(5,7), // random 5 or 6
	'length' => 6,

	// CAPTCHA image size (you do not need to change it, whis parameters is optimal)
	'width' => 250,
	'height' => 60,

	// symbol's vertical fluctuation amplitude divided by 2
	'fluctuation_amplitude' => 15,

	// increase safety by prevention of spaces between symbols
	'spaces' => 17,

	// CAPTCHA image colors (RGB, 0-255)
	'foreground_color' => array(0, 0, 50),
	'background_color' => array(255, 255, 255),
//	'foreground_color' => array(mt_rand(0,100), mt_rand(0,100), mt_rand(0,100)),
//	'background_color' => array(mt_rand(200,255), mt_rand(200,255), mt_rand(200,255)),

// тип изображения. доступные значения: jpg, jpeg, jpe, jfif, png, gif, any
// в случае указания any пытается создать сначала jpg потом png и если ничего другого не работает создает gif 
	'default_image_type' => 'any',
);
?>