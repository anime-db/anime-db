<?php
if (empty($_SESSION['captcha_keystring']) || $_SESSION['captcha_keystring'] != $this->getValue()){
	$this->error('captcha');
}
