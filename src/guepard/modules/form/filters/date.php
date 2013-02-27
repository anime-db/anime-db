<?php
if ($this->getValue()!='' && !preg_match('/^\d\d\.\d\d\.\d{4}$/', $this->getValue())){
	$this->error('date');
}
