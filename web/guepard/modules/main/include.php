<?php
require_once 'includes/root.php';
require_once 'start.php';

require 'classes/Template.php';
require 'classes/Main.php';
require 'classes/Page.php';
require 'classes/HTTP.php';
require 'classes/query/DB.php';
require 'classes/ComplexComponent.php';

define('LANG', Main::getLangID());
define('LANG_CHARSET', Main::getLangCharset());
define('TEMPLATE_ID', Main::getTemplateID());
define('IS_WIN', preg_match('/^win/i', PHP_OS));

ini_set('error_reporting', Main::isPHPDebug() ? E_ALL : 0);
ini_set('display_errors', Main::isPHPDebug());

ini_set('register_globals', 0);

ini_set('magic_quotes_gpc', 0);
ini_set('magic_quotes_runtime', 0);

ini_set('session.auto_start', 0);
ini_set('session.use_cookies', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.name', 'GSMSID');
ini_set('session.cookie_domain', Main::getCookieDomain());
ini_set('session.gc_maxlifetime', Main::getSessionLifeTime());

// права на кэш
define('FILE_CACHE_ACCESS', 0755);
define('DIR_CACHE_ACCESS', 0755);
// права на файлы ядра
define('FILE_CORE_ACCESS', 0755);
// права на сайт
define('FILE_SITE_ACCESS', 0775);
define('DIR_SITE_ACCESS', 0775);

HTTP::send('X-Powered-CMS: Gepard Site Manager');


session_start();


/*
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/classes/general/main.php");


IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/classes/general/database.php");


require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/classes/general/module.php"); 


require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/classes/".$DBType."/agent.php");
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/classes/".$DBType."/user.php");
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/classes/".$DBType."/event.php");
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/classes/".$DBType."/usertype.php");
*/