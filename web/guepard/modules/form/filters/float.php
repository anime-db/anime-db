<?php
if (!is_numeric($this->getValue()) || (float)$this->getValue()!=$this->getValue()){
	$this->error('float');
}
?>