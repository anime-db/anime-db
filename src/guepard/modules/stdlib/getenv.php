<?php
namespace stdlib;

/**
 * Расширение стандартной функции getenv()
 * 
 * @package	Standard Library
 * @author	Peter Gribanov
 * @since	06.10.2010
 * @version	1.1
 * @param	string
 * @return	string
 */
function & getenv($key){
	if (!is_array($_SERVER) || !isset($_SERVER[$key])){
		$_SERVER[$key] = \getenv($key);
	}
	return $_SERVER[$key];
}
?>