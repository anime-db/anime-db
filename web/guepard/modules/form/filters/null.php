<?php
if (!is_bool($this->options['default']) && $this->getSentValue()===null){
	$this->error('null');
}
?>