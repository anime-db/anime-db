<?php
namespace stdlib;

/**
 * Возвращает максимальный объем данных в байтах, которые можно отправить
 * 
 * @package	Standard Library
 * @author	Peter Gribanov
 * @since	07.10.2010
 * @version	1.1
 * @return	integer
 */
function &getPostMaxSize(){
	$max_file_size = 16777216;
	$_post   = @ini_get('post_max_size');
	$_upload = @ini_get('upload_max_filesize');
	$tmp = ($_upload > $_post) ? $_post : $_upload;
	if ($tmp){
		$max_file_size = $tmp;
		preg_match('/^(\d+)(\w+)$/', strtolower($max_file_size), $match);
		if ($match[2] == 'm'){
			$max_file_size = intval($max_file_size) * 1024 * 1024;
		} elseif ($match[2] == 'k'){
			$max_file_size = intval($max_file_size) * 1024;
		} else {
			$max_file_size = intval($max_file_size);
		}
	}
	return $max_file_size;
}
?>