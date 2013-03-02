<?php
if (!is_numeric($this->getValue()) || intval($this->getValue())!=$this->getValue()){
	$this->error('int');
}
