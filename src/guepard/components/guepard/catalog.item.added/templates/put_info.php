                   <?=$result['name_ru']?> (<?=$result['name_en']?>)

   <?=$result['info_genre']?>: <?=$result['genre']?>


   <?=$result['info_type']?>: <?=$result['type']?><?if($result['episodes_number']):?> (<?=$result['episodes_number']?> <?=$result['info_ep']?>.)<?endif?> <?=$result['duration']?> <?=$result['info_min']?>.

   <?=$result['info_date']?>: <?=$result['info_date_from']?> <?=$result['date_start']?><?if($result['date_end']):?> <?=$result['info_date_to']?> <?=$result['date_end']?><?endif?>


<?if($result['summary']):?>   <?=$result['info_summary']?>: <?=$result['summary']?>


<?endif?>
<?if($result['episodes']):?>   <?=$result['info_episodes']?>:


<?=$result['episodes']?>
<?endif?>