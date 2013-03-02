<?php
$params = array_merge(array(
	'id'		=> '',
	'class'		=> '',
	'disabled'	=> false,
	'length'	=> 0
), $this->getViewParams());

?><input type="text" name="<?=$this->getName()?>" value="<?=$this->getValue()?>"<?=
($params['id'] ? ' id="'.$params['id'].'"' : '')?><?=
($params['disabled'] ? ' disabled="disabled"' : '')?><?=
($params['length'] ? ' maxlength="'.$params['length'].'"' : '')
?> class="<?=($params['class'] ? $params['class'].' ' : '')?>datepicker" />