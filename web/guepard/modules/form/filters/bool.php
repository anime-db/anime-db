<?php
if (!is_bool($this->getValue()) && (!is_numeric($this->getValue())
	|| ($this->getValue()!=0 && $this->getValue()!=1))){
	$param = $this->getViewParams();

	if (!empty($param['value_no']) && !empty($param['value_yes'])){
		$this->error('bool', array('('.$param['value_no'].', '.$param['value_yes'].')'));
	} else {
		$this->error('bool', array(''));
	}
}
?>