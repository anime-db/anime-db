<?php
namespace stdlib;

/**
 * Возвращает максимальный объем данных в байтах, которые можно занести в память
 * 
 * @package	Standard Library
 * @author	Peter Gribanov
 * @since	07.10.2010
 * @version	1.1
 * @return	integer
 */
function &getMemoryLimit(){
	$memory_limit = 2097152;
	$tmp = @ini_get('memory_limit');
	if ($tmp){
		$memory_limit = $tmp;
		preg_match('/^(\d+)(\w+)$/', strtolower($memory_limit), $match);
		if ($match[2] == 'm'){
			$memory_limit = intval($memory_limit) * 1024 * 1024;
		} elseif ($match[2] == 'k'){
			$memory_limit = intval($memory_limit) * 1024;
		} else {
			$memory_limit = intval($memory_limit);
		}
	}
	return $memory_limit;
}
?>