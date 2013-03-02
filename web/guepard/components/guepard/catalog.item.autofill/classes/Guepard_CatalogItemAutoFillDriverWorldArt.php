<?php
require 'Guepard_CatalogItemAutoFillDriver.php';

/**
 * Драйвер поиска по world-art.ru для автозаполнения
 * 
 * @author	Peter Gribanov
 * @since	15.09.2011
 * @version	1.1
 */
class Guepard_CatalogItemAutoFillDriverWorldArt implements Guepard_CatalogItemAutoFillDriver {

	/**
	 * Результат поиска
	 * 
	 * @var string
	 */
	private $result = '';


	/**
	 * Поиск по имени
	 * 
	 * @param string $name
	 * @return boolen
	 */
	public function search($name){
		$url = 'http://www.world-art.ru/search.php?public_search=%s&global_sector=animation';
		$request = sprintf($url, urlencode(iconv('utf-8', 'windows-1251', $name)));
		$this->result = file_get_contents($request);
		return (bool)$this->result;
	}

	/**
	 * Проверяет является результатом поиска искомый элимент
	 * 
	 * @return mixid
	 */
	public function isDesiredResult(){
		return preg_match('/<meta .*?refresh.*?id=(\d+)/is', $this->result, $mat) ? $mat[1] : false;
	}

	/**
	 * Получает список результатов поиска
	 * 
	 * @return mixid
	 */
	public function getSearchResult(){
		preg_match_all('/<a href\s?=\s?.[\.\/\?a-z]+id=(\d+). class=.estimation.>(.+?)<\/a>/is', $this->result, $out, PREG_SET_ORDER);

		foreach ($out as $k=>$v){
			array_shift($out[$k]);
			$v[2] = str_replace('&nbsp;', ' ', $v[2]);
			$v[2] = iconv('windows-1251', 'utf-8', $v[2]);
			$out[$k][1] = $v[2];
		}
		return $out;
	}

	/**
	 * Получает данные по элименту
	 * 
	 * @param integer $id
	 * @return mixid
	 */
	public function getData($id){
		$result = $this->loadData($id);

//		file_put_contents(ROOT.'result.or.html', $result);

		$result = $this->clearData($result);

//		file_put_contents(ROOT.'result.html', $result);
//		$result = file_get_contents(ROOT.'result.html');

		$dom = new DOMDocument('1.0', 'utf-8');
		@$dom->loadHTML($result);
		$xpath = new DOMXpath($dom);
		$info = $xpath->query('/html/body/table[5]/tr/td/table/tr/td[5]/table[2]/tr/td[3]');


		$data = array(
			'external_id' => $id
		);

		if (is_null($info) || !$info->length){
			return false;
		}

		$info = $info->item(0)->childNodes;
		$info_in_line = '';
		foreach ($info as $node){
			$info_in_line .= $node->nodeValue.' ';
		}
		$info_in_line = str_replace('  ', ' ', $info_in_line);


		// названия
		$data['name_ru'] = substr($info->item(0)->nodeValue, 0, -2);
		$data['name_en'] = $info->item(4)->nodeValue;


		// производство
		preg_match('/Производство:\s*(\S+)/', $info_in_line, $mat);
		$q = DB::prepare('SELECT country_id FROM catalog_country WHERE LOWER(name) = LOWER(:name)');
		$q->bindValue(':name', $mat[1], PDO::PARAM_STR, 16);
		$q->execute();
		$data['production'] = $q->fetch()->country_id;


		// жанр
		preg_match('/Жанр:((?: \S+ ,?)+)Тип:/', $info_in_line, $mat);
		$genres = explode(' , ', trim($mat[1]));
		$params = array();
		foreach ($genres as $n=>$gen)
			$params[] = 'LOWER(name) = LOWER(:name'.$n.')';

		$q = DB::prepare('SELECT genre_id FROM catalog_genres WHERE '.implode(' OR ', $params));
		foreach ($genres as $n=>$gen)
			$q->bindValue(':name'.$n, $genres[$n], PDO::PARAM_STR, 64);

		$q->execute();
		$data['genre'] = array();
		while ($gen=$q->fetch())
			$data['genre'][] = $gen->genre_id;


		// тип и проболжительность серий
		preg_match('/Тип: (.+?)(?: \((?:>?\d+) эп\..*?\))?, (\d+) мин\./', $info_in_line, $mat);
		$q = DB::prepare('SELECT type_id FROM catalog_item_type WHERE name LIKE LOWER(:name)');
		$q->bindValue(':name', $mat[1].'%', PDO::PARAM_STR, 64);
		$q->execute();
		$data['type'] = $q->fetch()->type_id;

		// продолжительность
		$data['duration'] = $mat[2];


		// выпуск
		$data['date_end'] = '';
		if (preg_match('/Выпуск: c (\d+) \. (\d+) \. (\d+)(?: по  (\d+) \. (\d+) \. (\d+))?/', $info_in_line, $mat)){
			$data['date_start'] = $mat[1].'.'.$mat[2].'.'.$mat[3];
			if (isset($mat[4], $mat[5], $mat[6]))
				$data['date_end'] = $mat[4].'.'.$mat[5].'.'.$mat[6];
		} elseif (preg_match('/Премьера: (\d+) \. (\d+) \. (\d+)/', $info_in_line, $mat)){
			$data['date_start'] = $mat[1].'.'.$mat[2].'.'.$mat[3];
		}


		// картинка
		$img = $xpath->query('/html/body/table[5]/tr/td/table/tr/td[5]/table[2]/tr/td/a/img');
		if (!is_null($img) && $img->length){
			$data['image-url'] = 'http://www.world-art.ru/animation/'.$img->item(0)->attributes->getNamedItem('src')->nodeValue;
		}


		// описание
		$area = $xpath->query('/html/body/table[5]/tr/td/table/tr/td[5]');
		if (!is_null($area) && $area->length){
			// проходим по списку дочерних елементов
			for ($i=0; $i < $area->item(0)->childNodes->length; $i++){
				$block = $area->item(0)->childNodes;
				// ищем таблицу с нужным заголовком
				if ($block->item($i)->nodeName != 'table'
					|| trim($block->item($i)->childNodes->item(0)->childNodes->item(0)->nodeValue) != 'Краткое содержание:') continue;
				// берем данные из первой ячейки следующей таблици
				$data['summary'] = $block->item(++$i)->childNodes->item(0)->childNodes->item(0)->childNodes->item(0)->nodeValue;
				break;
			}
		}


		// эпизоды
		$area = $xpath->query('/html/body/table[5]/tr/td/table/tr/td[5]');
		if (!is_null($area) && $area->length){
			$text = '';
			$area = $area->item(0)->childNodes;
			// проходим по списку дочерних елементов
			foreach ($area as $i=>$block){
				// ищем таблицу с нужным заголовком
				if ($block->nodeName != 'table' || trim($block->childNodes->item(0)->childNodes->item(0)->nodeValue) != 'Эпизоды:') continue;
				// берем данные из первой ячейки следующей таблици
				$eps = $area->item(++$i)->childNodes->item(0)->childNodes->item(0)->childNodes;
				foreach ($eps as $n=>$ep){
					// нам нужен только текст
					if ($ep->nodeType !== XML_TEXT_NODE) continue;
					// для описания серий в таблицах используется другой алгоритм
					if (trim($ep->nodeValue) == '#'){
						for ($k=1; $k<$area->item($i)->childNodes->length; $k++){
							$ep = $area->item($i)->childNodes->item($k)->childNodes;
							// дополняем номер серии нулями
							$len = strlen($area->item($i)->childNodes->length)+1;
							$text .= str_pad($ep->item(0)->nodeValue, $len, 0, STR_PAD_LEFT);
							$text .= ' '.$ep->item(1)->childNodes->item(0)->nodeValue."\n";
						}
						break;
					}
					// сохраняем запись
					$text .= $ep->nodeValue."\n";
				}
				break;
			}
			$data['episodes'] = trim($text);
		}

		return $data;
	}

	/**
	 * Enter description here ...
	 * 
	 * @param integer $id
	 * @return string
	 */
	private function loadData($id){
		$url = 'http://www.world-art.ru/animation/animation.php?id=%s';
		$request = sprintf($url, urlencode($id));
		$result = file_get_contents($request);
		return iconv('windows-1251', 'utf-8', $result);
	}

	/**
	 * Enter description here ...
	 * 
	 * @param string $result
	 * @return string
	 */
	private function clearData($result){
		// простецшее приобразование
		$result = preg_replace('/&#\d+;/', '', $result);
		$result = str_replace('&nbsp;', ' ', $result);
		$result = str_replace('&', '&amp;', $result);
		$result = str_replace('&amp;amp;', '&amp;', $result);
		$result = str_replace('<br>', '<br />', $result);
		$result = str_replace('<</a>', '&lt;</a>', $result);

		// отбрасывание мусора
		$result = preg_replace('/<form.*?<\/form>/is', '', $result);
		$result = preg_replace('/<script.*?<\/script>/is', '', $result);
		$result = preg_replace('/<noscript>.*?<\/noscript>/is', '', $result);
		$result = preg_replace('/<\/?noindex>/i', '', $result);
		$result = preg_replace('/<embed.*?>/is', '', $result);
		$result = preg_replace('/<\/?center>/i', '', $result);
		$result = preg_replace('/<head>.*?<\/head>/is', '<head><title>replace</title></head>', $result);
		$result = preg_replace('/<\/?font.*?>/is', '', $result);
		$result = preg_replace('/<\/?b>/i', '', $result);
		$result = preg_replace('/<body.*?>/is', '<body>', $result);
		$result = preg_replace('/<!\-\-.*?\-\->/s', '', $result);

		// убираем alt иначе не получится с кавычками
		$result = preg_replace('/ alt=".+?"/is', '', $result);
		$result = preg_replace('/ alt=\'.+?\'/is', '', $result);
		// приводим img к Xhtml
		$result = preg_replace('/<img(.*?)>/is', '<img$1 alt="" />', $result);

		// кавычки
		$result = str_replace('>', ' >', $result);
		$result = preg_replace('/=\'(.*?)\' /s', '="$1" ', $result);
		$result = preg_replace('/ (\w+)\s*=\s*((?!\'|")\S+(?!\'|"))/', ' $1="$2"', $result);
		$result = str_replace(' >', '>', $result);

		// атрибуты
		$result = preg_replace('/ (border|v?align|background|bgcolor|width|height|cellspacing|cellpadding|style|target)=".+?"/is', '', $result);

		// исправление ошибок верстки
		$result = str_replace('<td></td><td class="line">', '</td><td class="line">', $result);
		$result = str_replace('<table><td class="line">', '<table><tr><td class="line">', $result);
		$result = str_replace('</td></tr></table></td></tr></table><table><tr><td>', '</td></tr></table><table>', $result);
		$result .= '</body></html>';

		// пустые таблицы
		$result = preg_replace('/\s*<table\s*>\s*<tr>\s*<td\s*( class="line")?\s*>\s*<\/td>\s*<\/tr>\s*<\/table>\s*/i', '', $result);

		// ставим доктупи
		$result = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">'.$result;
		$result = str_replace('<head>', '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />', $result);

		return $result;
	}

}