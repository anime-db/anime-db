<?php

require_once ROOT.G_ROOT.'modules/form/classes/FormComplex.php';

/**
 * Enter description here ...
 * 
 * @author gribape
 */
class Guepard_CatalogItemAdded implements FormComplex {

	/**
	 * Enter description here ...
	 * 
	 * @var Form
	 */
	private $form;

	/**
	 * Enter description here ...
	 * 
	 * @var array
	 */
	private $lang = array();

	/**
	 * Enter description here ...
	 * 
	 * @var array
	 */
	private $params = array(
		'link'		=> '',
		'show_link'	=> '#id#',
	);


	/**
	 * Enter description here ...
	 * 
	 * @return void
	 */
	public function __construct($params){
		$this->params = array_merge($this->params, $params);

		$this->lang = Main::getComponentLang('guepard:catalog.item.added');
		Page::setTitle($this->lang['title']);
	}

	/**
	 * Устанавливает объект формы
	 * 
	 * @param Form $form
	 * @return void
	 */
	public function setForm(Form $form){
		$this->form = $form;
	}

	/**
	 * Enter description here ...
	 * 
	 * @throws FormFilterException
	 * @return void
	 */
	public function valid(){
		$this->form->valid();
		if (!$this->form->getSentValue('image-url') && !$_FILES['image-file']['size'])
			throw new Exception($this->lang['not_image']);

		$path = $this->form->getSentValue('path');
		$path = IS_WIN ? iconv('utf-8', 'cp1251', str_replace('\\', '/', $path)) : $path;
		if (!is_dir($path) || !is_readable($path))
			throw new Exception($this->lang['not_read_path']);
	}

	/**
	 * Enter description here ...
	 * 
	 * @return void
	 */
	public function draw(){
		$this->form->addButton('Автозаполнение', array('type'=>'submit','id'=>'catalog-item-autofill'))->draw();
	}

	/**
	 * Enter description here ...
	 * 
	 * @return boolen
	 */
	public function isAlreadySent(){
		return $this->form->isAlreadySent();
	}

	/**
	 * Enter description here ...
	 * 
	 * @throws Exception
	 * @return void
	 */
	public function writeData(){
		try {
			if (false===DB::beginTransaction())
				throw new Exception($this->lang['write_error']);

			$q = DB::prepare('INSERT
INTO
	catalog_items
	(external_id, type_id, name_ru, name_en,
	date_start, date_end, country_id, summary,
	episodes, episodes_number, duration, path,
	storage_id, translate, sub_ru, file_info)
VALUES
	(:external_id, :type, :name_ru, :name_en,
	:date_start, :date_end, :production, :summary,
	:episodes, :episodes_number, :duration, :path,
	:storage_id, :translate, :sub_ru, :file_info)');

			$q->bindParam(':external_id', $this->form->getSentValue('external_id'), PDO::PARAM_INT, 6);
			$q->bindParam(':type', $this->form->getSentValue('type'), PDO::PARAM_STR, 12);
			$q->bindParam(':name_ru', $this->form->getSentValue('name_ru'), PDO::PARAM_STR, 12);
			$q->bindParam(':name_en', $this->form->getSentValue('name_en'), PDO::PARAM_STR, 12);

			list($d, $m, $y) = explode('.', $this->form->getSentValue('date_start'), 3);
			$date_start = mktime(0, 0, 0, $m, $d, $y);
			$q->bindParam(':date_start', $date_start, PDO::PARAM_INT, 10);

			list($d, $m, $y) = explode('.', $this->form->getSentValue('date_end'), 3);
			$date_end = mktime(0, 0, 0, $m, $d, $y);
			$q->bindParam(':date_end', $date_end, PDO::PARAM_INT, 10);

			$q->bindParam(':production', $this->form->getSentValue('production'), PDO::PARAM_STR, 2);
			$q->bindParam(':summary', $this->form->getSentValue('summary'), PDO::PARAM_STR);
			$q->bindParam(':episodes', $this->form->getSentValue('episodes'), PDO::PARAM_STR);
			$number = 0;
			$strs = trim($this->form->getSentValue('episodes'));
			if ($strs){
				$strs = explode("\n", $strs);
				while ($str=array_pop($strs)){
					if (preg_match('/^(\d+)(?: \(\d+\))?\. /', trim($str), $mat)){
						$number = $mat[1];
						break;
					}
				}
				if (!$number)
					throw new LogicException($this->lang['error_number_episodes']);
			}
			
			$q->bindParam(':episodes_number', $number, PDO::PARAM_STR);
			$q->bindParam(':duration', $this->form->getSentValue('duration'), PDO::PARAM_INT, 3);
			$q->bindParam(':path', $this->form->getSentValue('path'), PDO::PARAM_STR, 256);
			$q->bindParam(':storage_id', $this->form->getSentValue('storage_id'), PDO::PARAM_STR, 32);
			$q->bindParam(':translate', $this->form->getSentValue('translate'), PDO::PARAM_STR, 64);
			$q->bindParam(':sub_ru', $this->form->getSentValue('sub_ru'), PDO::PARAM_INT, 1);
			$q->bindParam(':file_info', $this->form->getSentValue('file_info'), PDO::PARAM_STR);

			$q->execute();
			$id = DB::lastInsertId();

			foreach ($this->form->getSentValue('genre') as $genre){
				$q = DB::prepare('INSERT
INTO
	catalog_item_to_genre
	(item_id, genre_id)
VALUES
	(:item_id, :genre_id)');
				$q->bindParam(':item_id', $id, PDO::PARAM_INT, 6);
				$q->bindParam(':genre_id', $genre, PDO::PARAM_INT, 2);
				$q->execute();
			}

			$this->packDataIntoDir($id, $this->form->getSentValue('path'), $number);
			DB::commit();
			$this->form->clearSentValues();
			Main::redirect((strpos($this->params['link'], '?')!==false ? '&' : '?').'secid='.$id);
		} catch (Exception $e){
    		DB::rollBack();
    		throw $e;
		}
	}

	/**
	 * Enter description here ...
	 * 
	 * @param integer $id
	 * @param string $path
	 * @param integer $number
	 * @throws Exception
	 * @return void
	 */
	private function packDataIntoDir($id, $path, $number){
		// ссылка на картинку
		$image_path = ROOT.'upload/catalog/'.$id.'.';
		if ($_FILES['image-file']['size']){
			$image_ext = pathinfo($_FILES['userfile']['name'], PATHINFO_EXTENSION);
			if (!move_uploaded_file($_FILES['userfile']['tmp_name'], $image_path.$image_ext))
				throw new Exception($this->lang['error_image_upload']);
		} else {
			$image_ext = pathinfo($this->form->getSentValue('image-url'), PATHINFO_EXTENSION);
			if (!copy($this->form->getSentValue('image-url'), $image_path.$image_ext))
				throw new Exception($this->lang['error_image_upload']);
		}
		$path = IS_WIN ? iconv('utf-8', 'cp1251', $path) : $path;
		$path = str_replace('\\', '/', realpath($path));
		if (!file_exists($path.'/cover.'.$image_ext) && !copy($image_path.$image_ext, $path.'/cover.'.$image_ext)){
			@unlink($image_path.$image_ext);
			throw new Exception($this->lang['error_image_copy']);
		}


		// ссылка на запись в бд
		file_put_contents($path.'/db.url', 'URL='.HOST.str_replace('#id#', $id, $this->params['show_link']));


		// краткое описание аниме
		$buffer = ob_get_contents();
		ob_clean();

		// получение жанра
		$params = array();
		foreach ($this->form->getSentValue('genre') as $n=>$gen)
			$params[] = 'genre_id = :genre_id'.$n;

		$q = DB::prepare('SELECT name FROM catalog_genres WHERE '.implode(' OR ', $params));
		foreach ($this->form->getSentValue('genre') as $n=>$gen)
			$q->bindValue(':genre_id'.$n, $gen, PDO::PARAM_INT, 2);

		$q->execute();
		$genre = array();
		while ($gen=$q->fetch())
			$genre[] = $gen->name;

		// получение типа
		$type = DB::prepare('SELECT name FROM catalog_item_type WHERE type_id=:type_id');
		$type->bindValue(':type_id', $this->form->getSentValue('type'), PDO::PARAM_STR, 12);
		$type->execute();

		// загрузка шаблона
		Main::getComponentTemplate('guepard:catalog.item.added', 'put_info.php', array(
			'name_ru'			=> $this->form->getSentValue('name_ru'),
			'name_en'			=> $this->form->getSentValue('name_en'),
			'genre'				=> implode(', ', $genre),
			'type'				=> $type->fetch()->name,
			'episodes_number'	=> $number,
			'duration'			=> $this->form->getSentValue('duration'),
			'date_start'		=> $this->form->getSentValue('date_start'),
			'date_end'			=> $this->form->getSentValue('date_end'),
			'summary'			=> $this->form->getSentValue('summary'),
			'episodes'			=> $this->form->getSentValue('episodes'),
			'info_genre'		=> $this->lang['info_genre'],
			'info_type'			=> $this->lang['info_type'],
			'info_date'			=> $this->lang['info_date'],
			'info_date_from'	=> $this->lang['info_date_from'],
			'info_date_to'		=> $this->lang['info_date_to'],
			'info_ep'			=> $this->lang['info_ep'],
			'info_min'			=> $this->lang['info_min'],
			'info_summary'		=> $this->lang['info_summary'],
			'info_episodes'		=> $this->lang['info_episodes'],
		));
		// запись шаблона в файл
		$info = ob_get_contents();
		file_put_contents($path.'/info.txt', $info);
		ob_clean();
		echo $buffer;
	}

	/**
	 * Enter description here ...
	 */
	public function loadForm(){
		Page::IncludeComponent('guepard:form', '', array(
			'form_name'		=> 'catalog_item_added'
		), $this);
	}

	/**
	 * Enter description here ...
	 * 
	 * @param integer $id
	 * @return void
	 */
	public function successful($id){
		Main::getComponentTemplate('guepard:catalog.item.added', 'successful.php', array(
			'link'	=> str_replace('#id#', $id, $this->params['show_link']),
			'msg'	=> $this->lang['successful'],
		));
	}

	/**
	 * Enter description here ...
	 * 
	 * @param Exception $e
	 * @return void
	 */
	public function exception(Exception $e){
		Main::getComponentTemplate('guepard:catalog.item.added', 'error.php', array(
			'title'	=> $this->lang['error_title'],
			'msg'	=> $e->getMessage(),
			'code'	=> $e->getCode(),
		));
	}

}