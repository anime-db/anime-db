<h1>Форма обратной связи на сайте <a href="<?=HOST?>"><?=$_SERVER['HTTP_HOST']?></a></h1>
<strong><?=$form->getSentValue('subject')?></strong><br />
<br />
<strong>Имя отправителя:</strong> <?=$form->getSentValue('name')?><br />
<strong>E-mail для связи:</strong> <?=$form->getSentValue('mail')?><br />
<strong>Текст сообщение:</strong><br />
<?=$form->getSentValue('textarea')?>