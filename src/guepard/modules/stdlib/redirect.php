<?php
namespace stdlib;

/**
 * HTTP редирект на другую страницу
 * 
 * @package	Standard Library
 * @author	Peter Gribanov
 * @since	07.10.2010
 * @version	2.0
 * @param	string	URL to load
 * @return	void
 */
function redirect($url){
	@header('Location: '.$url);
	echo 'Forwarding to: '.$url;
	exit;
}
?>