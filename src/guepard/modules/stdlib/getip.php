<?php
namespace stdlib;

/**
 * Получение IP адреса с которым пришел пользователь
 * 
 * @package	Standard Library
 * @author	Peter Gribanov
 * @since	04.12.2010
 * @version	2.3
 * @return	string	IP
 */
function getip(){
	// Отсортировать доступные IP адресв
	$addrs = array();
	foreach (array_reverse(explode(',', getenv('HTTP_X_FORWARDED_FOR'))) as $x_f){
		$x_f = trim($x_f);
		if (preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/', $x_f)) $addrs[] = & $x_f;
	}
	$addrs[] = & getenv('HTTP_CLIENT_IP');
	$addrs[] = & getenv('HTTP_X_CLUSTER_CLIENT_IP');
	$addrs[] = & getenv('HTTP_PROXY_USER');
	$addrs[] = & getenv('REMOTE_ADDR');

	// Получение IP
	foreach ($addrs as $ip){
		if (!$ip) continue;
		preg_match('/^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/', $ip, $match);
		$ip_address = $match[1].'.'.$match[2].'.'.$match[3].'.'.$match[4];
		if ($ip_address && ($ip_address != '...')) break;
	}

	// Убедится что пользователь не аноним
	if ((!$ip_address || ($ip_address=='...')) && !isset($_SERVER['SHELL']))
		exit('Could not determine your IP address.');

	return $ip_address;
}
?>