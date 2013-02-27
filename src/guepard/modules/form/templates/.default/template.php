<? if ($this->getName()):?><div id="form-<?=$this->getName()?>">
<? endif?>
<form action="<?=$this->getAction()?>" method="<?=$this->getMethod()?>"<?=
($this->getName() ? ' name="'.$this->getName().'"' : '')?> enctype="multipart/form-data">
<? $this->getCollection()->draw()?>
<div class="form-buttons">
<? $this->drawButtons()?>
<button type="submit"><? if($this->getSubmitTitle()):?><?=$this->getSubmitTitle()?><? else:?><?=$this->getLangPost('default_submit_title')?><? endif?></button>
</div>
</form>
<? if($this->isRequired()):?>
<div class="form-field-required-comment">
	<span class="form-field-required">*</span> - <?=$this->getLangPost('required_fields')?>
</div>
<? endif?>
<? if ($this->getName()):?></div><? endif?>
<script type="text/javascript">
$(function(){
	$('.datepicker').datepicker({dateFormat:'dd.mm.yy'});
	// captcha
	$('.form-captcha-button').click(function(e){
		$('.form-captcha-image').attr('src',
			$('.form-captcha-image').attr('src').replace(/(\?.*?)(_=\d+)?/, '$1')
			+'_='+(new Date()).getTime());
		e.stopImmediatePropagation();
		return false;
	});
});
</script>