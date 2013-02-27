<?php
$params = array_merge(array(
	'id'		=> '',
	'class'		=> '',
), $this->getViewParams());

?><input type="hidden" name="<?=$this->getName()?>" value="<?=$this->getValue()?>"<?=
($params['class'] ? ' class="'.$params['class'].'"' : '')?><?=
($params['id'] ? ' id="'.$params['id'].'"' : '')?> />