<?php
namespace stdlib;

/**
 * Возвращает MIME тип определяя его по расширению файла
 * 
 * @package	Standard Library
 * @author	Peter Gribanov
 * @since	07.10.2010
 * @version	1.1
 * @param	string
 * @return	string
 */
function getMimeType($filename){
	$reg = '/^(\S+)\s+.*'.strtolower(pathinfo($filename, PATHINFO_EXTENSION)).'.*$/';
	if (($fp=@fopen(dirname(__FILE__).'mime.types', 'r'))!==false){
		while(!feof($fp)){
			$type = fread($fp, 4096);
			if ($type[0]!='#' && preg_match($reg, $type, $m)) return $m[1];
		}
		fclose($fp);
	}
	return 'application/octet-stream';
}
?>