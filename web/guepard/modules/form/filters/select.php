<?php
$params = array_merge(array(
	'use_key'	=> false,
	'options'	=> array(),
	'multiple'	=> false,
), $this->getViewParams());

$values = $params['use_key'] ? array_keys($params['options']) : array_values($params['options']);
$values[] = 100;

if ($params['multiple']){
	foreach ($this->getValue() as $value)
		if (!in_array($value, $values))
			$this->error('select');

} elseif (!in_array($this->getValue(), $values)){
	$this->error('select');
}
?>