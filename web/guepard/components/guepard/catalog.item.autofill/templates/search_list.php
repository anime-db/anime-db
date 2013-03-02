<strong><?=$result['title']?></strong><br />
<ul class="catalog-item-autofill-search-list">
<?foreach ($result['list'] as $item):?>
	<li><a href="?id=<?=$item[0]?>"><?=$item[1]?></a></li>

<?endforeach?>
</ul>
<br />
<?=sprintf($result['return'], '<a href="">', '</a>')?>
