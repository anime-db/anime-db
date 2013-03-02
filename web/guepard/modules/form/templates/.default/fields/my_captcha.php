<? include 'captcha-image.php'?>
<br />
<div class="comment"><?=sprintf($this->getLangPost('captcha-length'), $params['length'])?><br />
<?=sprintf($this->getLangPost('captcha-link'), '<a href="" id="form-captcha-button" class="global-link">', '</a>')?></div>
<? include 'captcha-field.php'?>
<script type="text/javascript">
$(function(){
	$('#form-captcha-button').click(function(e){
		$('#form-captcha-image').attr('src',
			$('#form-captcha-image').attr('src').replace(/(\?.*?)(_=\d+)?/, '$1')
			+'_='+(new Date()).getTime());
		e.stopImmediatePropagation();
		return false;
	});
});
</script>