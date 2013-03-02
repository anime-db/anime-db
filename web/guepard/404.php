<?php
if (defined('G_PROLOG_INCLUDED')){
	include 'modules/main/includes/prolog_after.php';
} else {
	include 'header.php';
}
HTTP::setStatus('404 Not Found');
Page::setTitle('Error: 404 Not Found');
?>
<h1>404 Not Found</h1>
<p>The requested URL <?=$_SERVER['REQUEST_URI']?> was not found on this server.</p>
<? require 'footer.php'?>