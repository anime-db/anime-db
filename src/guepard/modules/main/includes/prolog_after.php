<?php
HTTP::send('Content-type: text/html; charset='.LANG_CHARSET);
HTTP::setCacheControl();

Main::PrologActions();

Page::addCSS('style.css');

ob_start();

include(ROOT.G_ROOT.'templates/'.TEMPLATE_ID.'/header.php');

define('START_EXEC_PROLOG_AFTER', microtime());
