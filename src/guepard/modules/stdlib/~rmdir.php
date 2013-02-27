<?php
namespace stdlib;

/**
 * Рекурсивно удаляет дирректорию
 * 
 * @package	Standard Library
 * @author	Peter Gribanov
 * @since	04.12.2010
 * @version	1.0
 * @param	string
 * @return	boolen
 */
function rmdir($dirname){
	$files = scandir($dirname);
	array_shift($files); // remove '.' from array
	array_shift($files); // remove '..' from array

	foreach ($files as $file) {
		$file = $dirname . '/' . $file;
		$result = is_dir($file) ? rmdir($file) : unlink($file);
		if (!$result) return false;
	}
	return \rmdir($dirname);
}