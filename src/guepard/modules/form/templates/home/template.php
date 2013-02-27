<form action="<?=$this->getAction()?>" method="<?=$this->getMethod()?>">
<? $this->getCollection()->draw()?>
<button type="submit"><? if($this->getSubmitTitle()):?><?=$this->getSubmitTitle()?><? else:?><?=$this->getLangPost('default_submit_title')?><? endif?></button>
</form>