<? if($this->getName()):?>
<fieldset>
  <legend><?=$this->getName()?></legend>
<? endif?>
<table class="form-fields">
<? foreach ($this as $item):?>
<? $item->draw()?>
<? endforeach?>
</table>
<? if($this->getName()):?>
</fieldset>
<? endif?>