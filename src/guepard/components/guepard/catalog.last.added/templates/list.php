<div class="catalog-last-added">
	<h2><?=$result['lang']['title']?></h2>
<?foreach($result['list'] as $item):?>
<?
$name = $item['name_ru'] ? $item['name_ru'] : ($item['name_en'] ? $item['name_en'] : $item['name_jp']);
?>
	<div class="item">
		<a href="<?=$item['link']?>" title="<?=$result['lang']['details_title']?>: <?=$name?>" class="cover">
			<img src="/upload/catalog/<?=$item['item_id']?>/w240.jpg" alt="<?=$item['name_ru']?>" />
		</a>
		<div class="info">
			<div class="name">
				<strong><?=$name?></strong><br />
<?if($item['name_en'] && $item['name_en']!=$name):?>				<span><?=$item['name_en']?></span><br /><?endif?>
<?if($item['name_jp'] && $item['name_jp']!=$name):?>				<span><?=$item['name_jp']?></span><br /><?endif?>
			</div>
			<div class="date"><strong><?=$result['lang']['info_date']?></strong>: <span><?=$result['lang']['info_date_from']?> <?=date('d.m.Y', $item['date_start'])?><?if($item['date_end']):?> <?=$result['lang']['info_date_to']?> <?=date('d.m.Y', $item['date_end'])?><?endif?></span></div>
			<div class="genre"><strong><?=$result['lang']['info_genre']?></strong>: <span><?foreach($item['genres'] as $i=>$genre):?><?if($i):?>, <?endif?><a href="<?=$genre['link']?>" title="<?=$result['lang']['genre_title']?>: <?=$genre['name']?>"><?=$genre['name']?></a><?endforeach?></span></div>
			<div class="type"><strong><?=$result['lang']['info_type']?></strong>: <span><a href="<?=$item['type']?>" title="<?=$result['lang']['type_title']?>: <?=$item['type_name']?>"><?=$item['type_name']?></a><?if($item['episodes_number']):?>, <?=$item['episodes_number']?> <?=$result['lang']['info_ep']?>.<?endif?>, <?=$item['duration']?> <?=$result['lang']['info_min']?>.</span></div>
			<strong><?=$result['lang']['info_summary']?></strong>:<br />
			<div class="summary"><?=$item['summary']?></div>
		</div>
		<a href="<?=$item['link']?>" title="<?=$result['lang']['details_title']?>: <?=$name?>" class="details"><?=$result['lang']['details']?></a>
	</div>
<?endforeach?>
</div>
