<?php
require_once 'includes/root.php';

function getmicrotime(){
	list($usec, $sec) = explode(' ', microtime());
	return ((float)$usec + (float)$sec);
}

define('START_EXEC_TIME', getmicrotime());
define('G_PROLOG_INCLUDED', true);
