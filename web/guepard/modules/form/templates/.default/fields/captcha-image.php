<?php
$params = array_merge(array(
	'width'		=> 0,
	'height'	=> 0,
	'length'	=> 0,
), $this->getViewParams());

?><img src="<?=G_ROOT?>components/guepard/form/images/captcha/captcha.jpg?<?=
($params['width'] ? 'w='.$params['width'].'&' : '')?><?=
($params['height'] ? 'h='.$params['height'].'&' : '')?><?=
($params['length'] ? 'l='.$params['length'].'&' : '')?>_=<?=time()?>" class="form-captcha-image" alt="" />