<?php
$params = array_merge(array(
	'id'		=> '',
	'class'		=> '',
	'disabled'	=> false
), $this->getViewParams());
	
// преобразование в bool если значение было указано некорректно
if (!is_bool($this->getDefaultValue())){
	$this->setDefaultValue((bool)$this->getDefaultValue());
}

?><input type="checkbox" name="<?=$this->getName()?>"<?=
($params['class'] ? ' class="'.$params['class'].'"' : '')?><?=
($params['id'] ? ' id="'.$params['id'].'"' : '')?><?=
($params['disabled'] ? ' disabled="disabled"' : '')?><?=
($this->getValue() ? ' checked="checked"' : '')?> />