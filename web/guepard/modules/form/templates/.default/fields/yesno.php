<?php
$params = array_merge(array(
	'id'		=> '',
	'class'		=> '',
	'disabled'	=> false,
	'value_no'	=> $this->getLangPost('field-yesno-no'),
	'value_yes'	=> $this->getLangPost('field-yesno-yes')
), $this->getViewParams());

$label = ($params['id'] ? $params['id'] : 'form-field-yesno-'.$this->getName()).'-';

if($params['class'] || $params['id']):
?><div<?=
($params['class'] ? ' class="'.$params['class'].'"' : '')?><?=
($params['id'] ? ' id="'.$params['id'].'"' : '')?>>
<?endif?>
<label for="<?=$label?>no"><?=$params['value_no']?></label>
<input type="radio" name="<?=$this->getName()?>" value="0" id="<?=$label?>no"<?=
($params['class'] ? ' class="'.$params['class'].'-no"' : '')?><?=
(!$this->getValue() ? ' checked="checked"' : '')?><?=
($params['disabled'] ? ' disabled="disabled"' : '')
?> /> <input type="radio" name="<?=$this->getName()?>" value="1" id="<?=$label?>yes"<?=
($params['disabled'] ? ' disabled="disabled"' : '')?><?=
($params['class'] ? ' class="'.$params['class'].'-yes"' : '')?><?=
($this->getValue() ? ' checked="checked"' : '')
?> />
<label for="<?=$label?>yes"><?=$params['value_yes']?></label><?
if($params['class'] || $params['id']):?>
</div>
<?endif?>