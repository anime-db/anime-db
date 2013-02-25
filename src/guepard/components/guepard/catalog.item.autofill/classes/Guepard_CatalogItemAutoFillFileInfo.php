<?php
require 'Guepard_CatalogItemAutoFillDriver.php';

/**
 * Драйвер чтения информации о файлах для автозаполнения
 * 
 * @author	Peter Gribanov
 * @since	15.09.2011
 * @version	1.0
 */
class Guepard_CatalogItemAutoFillFileInfo {

	/**
	 * 
	 * 
	 * @param string $path
	 * @return boolen
	 */
	public function getFileInfo($path){
		$path = IS_WIN ? iconv('utf-8', 'cp1251', $path) : $path;
		$path = str_replace('\\', '/', realpath($path));

		var_dump(scandir($path));
	}

}