<?php
/*
$fname1 = __FILE__;
$fname2 = $_SERVER["DOCUMENT_ROOT"];
if(strpos($fname1, $fname2)!==0)
{
        $fname1 = realpath(__FILE__);
        $fname2 = realpath($_SERVER["DOCUMENT_ROOT"]);
}

if(strpos($fname1, $fname2)===0)
{
        $fname3 = RTrim($_SERVER["DOCUMENT_ROOT"], " /\\");
        $bx_root = substr($fname1, strlen($fname3));
        $bx_root = substr($bx_root, 0, strlen($bx_root) - strlen("/modules/main/include.php"));
}
else
        $bx_root = "/bitrix";

$bx_root = str_replace("\\", "/", $bx_root);
*/

$g_root = 'guepard/';
$root = $_SERVER['DOCUMENT_ROOT'].'/';

define('G_ROOT', $g_root);
define('ROOT', $root);

define('HOST', 'http'.($_SERVER['SERVER_PROTOCOL'][4]=='S' ? 's' : '').'://'.$_SERVER['HTTP_HOST'].'/');
