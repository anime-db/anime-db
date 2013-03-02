<?php
$params = array_merge(array(
	'id'		=> '',
	'cols'		=> 20,
	'rows'		=> 2,
	'class'		=> '',
	'disabled'	=> false
), $this->getViewParams());

?><textarea name="<?=$this->getName()?>"<?=
($params['cols'] ? ' cols="'.$params['cols'].'"' : '')?><?=
($params['rows'] ? ' rows="'.$params['rows'].'"' : '')?><?=
($params['class'] ? ' class="'.$params['class'].'"' : '')?><?=
($params['id'] ? ' id="'.$params['id'].'"' : '')?><?=
($params['disabled'] ? ' disabled="disabled"' : '')?>><?=$this->getValue()?></textarea>