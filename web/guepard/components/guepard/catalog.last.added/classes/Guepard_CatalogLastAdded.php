<?php

require_once 'Guepard_CatalogLastAddedComplex.php';

/**
 * Класс компонента списка последних добавленных эелементов
 * 
 * @author gribape
 */
class Guepard_CatalogLastAdded {

	/**
	 * Параметры компонента
	 * 
	 * @var	array
	 */
	private $params = array(
		'number'		=> 1,
		'genre_link'	=> '#genre#',
		'type_link'		=> '#type#',
		'show_link'		=> '#id#',
		'summary_wrap'	=> 0,
	);

	/**
	 * Список последних добавленных
	 * 
	 * @var	array
	 */
	private $result = array();


	/**
	 * Конструктор
	 * 
	 * @param	array	$params	Параметры компонента
	 * @return	void
	 */
	public function __construct($params){
		$this->params = array_merge($this->params, $params);
		$this->leadList();
	}

	/**
	 * Устанавливает значение для родительского компонента
	 * 
	 * @param	Guepard_CatalogLastAddedComplex	$component	Комплексный компонент
	 * @return	void
	 */
	public function setParentComponent(Guepard_CatalogLastAddedComplex $component){
		$component->setList($this->result);
	}

	/**
	 * Рисует список элементов
	 * 
	 * @return	void
	 */
	public function drow(){
		Main::getComponentTemplate('guepard:catalog.last.added', 'list.php', array(
			'list'			=> $this->result,
			'lang'			=> Main::getComponentLang('guepard:catalog.last.added'),
		));
	}

	/**
	 * Проверяет пуст ли список элиментов
	 * 
	 * @return	boolen	Результат проверки
	 */
	public function isEmptyList(){
		return empty($this->result);
	}

	/**
	 * Загружает список последних добавленных
	 * 
	 * @return	void
	 */
	private function leadList(){

		$items = DB::prepare('
SELECT
	`item_id`,
	`cit`.`type_id`,
	`cit`.`name` AS `type_name`,
	`name_ru`,
	`name_en`,
	`name_jp`,
	`date_start`,
	`date_end`,
	`summary`,
	`episodes_number`,
	`duration`
FROM
	`catalog_items` AS `ci`,
	`catalog_item_type` AS `cit`
WHERE
	`ci`.`type_id` = `cit`.`type_id`
ORDER BY
	`item_id` DESC
LIMIT
	0, :number');
		$items->bindValue(':number', $this->params['number'], PDO::PARAM_INT);
		$items->execute();

		while ($item=$items->fetch(PDO::FETCH_ASSOC)){
			$item['genres'] = $this->getGenres($item['item_id']);
			$item['type'] = str_replace('#type#', $item['type_id'], $this->params['type_link']);;
			$item['link'] = str_replace('#id#', $item['item_id'], $this->params['show_link']);
			// укорачивание текста и добавление в конце троиточия
			if ($this->params['summary_wrap'] && strlen($item['summary'])>$this->params['summary_wrap']){
				list($item['summary'], ) = explode('@@@', wordwrap($item['summary'], $this->params['summary_wrap'], '@@@'), 2);
				if (preg_match('/[\.\?!,;:]$/', $item['summary']))
					$item['summary'] = substr($item['summary'], 0, -1);
				$item['summary'] .= '...';
			}
			$this->result[] = $item;
		}
	}

	/**
	 * Возвращает список жанров для элемента
	 * 
	 * @param	integer	$item_id	ID элемента
	 * @return	array
	 */
	private function getGenres($item_id){
		$genres = DB::prepare('
SELECT
	`name`,
	`symbolic` AS `link`
FROM
	`catalog_genres` AS `cg`,
	`catalog_item_to_genre` AS `citg`
WHERE
	`citg`.`genre_id` = `cg`.`genre_id` AND
	`item_id` = :item_id');
		$genres->bindValue(':item_id', $item_id, PDO::PARAM_INT, 6);
		$genres->execute();
		$list = array();
		while ($genre=$genres->fetch(PDO::FETCH_ASSOC)){
			$genre['link'] = str_replace('#genre#', $genre['link'], $this->params['genre_link']);
			$list[] = $genre;
		}
		return $list;
	}

}