<?php
$params = array_merge(array(
	'id'		=> '',
	'class'		=> '',
	'disabled'	=> false,
	'type'		=> 'button',
	'name'		=> '',
	'value'		=> '',
), $params);

?><button<?=
($params['type'] ? ' type="'.$params['type'].'"' : '')?><?=
($params['id'] ? ' id="'.$params['id'].'"' : '')?><?=
($params['class'] ? ' class="'.$params['class'].'"' : '')?><?=
($params['disabled'] ? ' disabled="disabled"' : '')?><?=
($params['name'] ? ' name="'.$params['name'].'"' : '')?><?=
($params['value'] ? ' value="'.$params['value'].'"' : '')?>><?=$title?></button>