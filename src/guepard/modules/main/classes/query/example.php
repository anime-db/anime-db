<?php
// класс бд
include_once 'DB.php';


class DemoDecorator extends DBQuery {

	// Шаблон ссылки
	private $link;

	// Поля для заполнения
	private $fields;

	public function __construct(DBQuery $query, $link, $fields=array()){
		parent::__construct($query);
		$this->link = $link;
		$this->fields = $fields;
	}

	// Возвращает один элемент
	public function fetch($class_name=null, $ctor_args=null, $cursor_offset=0){
		if ($item=parent::fetch(PDO::FETCH_OBJ)){
			$fields = array($this->link);
			foreach ($this->fields as $field){
				$fields[] = $item->$field;
			}
			$item->link = call_user_func_array('sprintf', $fields);
			$item->test = true;
		}
		return $item;
	}
	
}


try {

	$q = new DemoDecorator(
		DB::prepare('SELECT * FROM catalog_items WHERE item_id=:item'),
		'/product.php?id=%s',
		array('item_id')
	);

	$q->bindValue('item', 1, PDO::PARAM_INT);

	echo 'Execute:';
	var_dump($q->execute());
	echo 'Count rows:';
	var_dump($q->rowCount());
	echo 'Ferst column:';
	var_dump($q->fetch());

} catch (Exception $e){
	exit('Error: '.$e->getMessage());
}

