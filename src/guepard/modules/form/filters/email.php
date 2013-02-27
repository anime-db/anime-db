<?php
// Check for @ symbol
if (substr_count($this->getValue(), '@') != 1){
	$this->error('email');
}
if (function_exists('filter_var') && !filter_var($this->getValue(), FILTER_VALIDATE_EMAIL)){
	$this->error('email');
}
//$reg = '/^(?:[-a-z0-9])+@(?:[-a-z0-9]{2,}\.)+(?:[a-z]{2,4}|[0-9]{1,4})$/i';
$reg = file_get_contents(dirname(__FILE__).'/mail.address.validation.re');
if (!preg_match($reg, $this->getValue())){
	$this->error('email');
}
?>