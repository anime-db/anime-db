<? defined('G_PROLOG_INCLUDED') or exit?>
<?
Page::addCSS('jquery-ui-1.8.16.custom.css');
Page::addJS('js/jquery-1.6.2.min.js');
Page::addJS('js/jquery-ui-1.8.16.custom.min.js');
Page::addJS('js/init.js');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?Page::showHead()?>
<title><?Page::showTitle()?></title>
</head>
<body>
<div id="bg">
	<div id="wrapper">
		<div id="header">
			<h1 class="logo">
				<a href="/" title="Home Anime DB"><span>Home Anime DB</span></a>
			</h1>
			<div id="navigation">
				<div class="left"></div>
				<ul>
					<li><a href="/alphabet.php">Выборка по алфавиту</a></li>
					<li><a href="/selection.php">Расширенная выборка</a></li>
					<li class="last"><a href="/item-added.html">Добавить запись</a></li>
				</ul>
				<div class="right"></div>
			</div>
		</div>
		<div id="content-wrapper">
			<div id="content">
				<div class="content">
					<div id="home-center-header">
						<p class="intro-message"><strong>Anime DB</strong> это система для составления домашней аниматеки. Система предназначена только для домашнего использования.</p>
						<div id="home-search">
							<h2>Поиск по сайту</h2>
							<? Page::IncludeComponent('guepard:form', '', array(
									'form_name'	=> 'home_search',
								))?>
						</div>
					</div>
					<div id="home-center">
