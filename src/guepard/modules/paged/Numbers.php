<?php
namespace paged;

/**
 * Класс для составления списка номеров страниц
 *
 * @package	Paged
 * @author	Peter Gribanov
 * @since	18.11.2010
 * @version	3.4
 */
class Numbers {

	/**
	 * Номер активной страницы
	 *
	 * @var	integer
	 */
	private $active = 1;

	/**
	 * Номер последней страници
	 *
	 * @var	integer
	 */
	private $last = 1;

	/**
	 * Начало списка номеров
	 * 
	 * @var	integer
	 */
	private $start = 1;

	/**
	 * Конец списка номеров
	 * 
	 * @var	integer
	 */
	private $end = 1;

	/**
	 * Длинна списка номеров страниц
	 * 
	 * @var	integer
	 */
	private $list_length = 0;


	/**
	 * Конструктор
	 * 
	 * @return	void
	 */
	protected function __construct(){
	}

	/**
	 * Инициализатор класса
	 * 
	 * @param	integer
	 * @return	Numbers
	 */
	public static function create($last){
		$n = new self();
		return $n->setLast($last);
	}

	/**
	 * Устанавливает длинну списка ссылок
	 *
	 * @param	integer
	 * @return	Numbers
	 */
	public function setListLength($length=0){
		if (!is_int($length) || intval($length)!=$length || $length<0){
			throw new InvalidArgumentException('Length list should be an integer number');
		}
		$this->list_length = intval($length);
		$this->calculateList();
		return $this;
	}

	/**
	 * Проверяет виден ли указанный номер страницы
	 *
	 * @param	integer
	 * @return  boolen
	 */
	public function isVisible($number){
		if (!is_int($number) || intval($number)!=$number || $number<0){
			throw new InvalidArgumentException('Page number should be an integer number');
		}
		return $number>=$this->start && $number<=$this->end;
	}

	/**
	 * Проверяет является ли список страниц пустым
	 * 
	 * @return	boolen
	 */
	public function isEmptyList(){
		return $this->end==1;
	}

	/**
	 * Устанавливает номер последней страницы
	 *
	 * @param	integer
	 * @return	Numbers
	 */
	public function setLast($last){
		if (!is_int($last) || intval($last)!=$last || $last<1){
			throw new InvalidArgumentException('Last page should be an integer number');
		}
		$this->last = intval($last);
		$this->end = $this->last;
		$this->calculateList();
		return $this;
	}

	/**
	 * Устанавливает номер активной страницы
	 *
	 * @param	integer
	 * @return	Numbers
	 */
	public function setActive($active){
		if (!is_int($active) || intval($active)!=$active || $active<1){
			throw new InvalidArgumentException('Active page should be an integer number');
		}
		$this->active = intval($active);
		$this->calculateList();
		return $this;
	}

	/**
	 * Возвращает номер активной страницы
	 *
	 * @return	integer
	 */
	public function getActive(){
		return $this->active;
	}

	/**
	 * Возвращает номер первой страницы
	 *
	 * @return	integer
	 */
	public function getFirst(){
		return 1;
	}

	/**
	 * Возвращает номер последней страницы
	 *
	 * @return	integer
	 */
	public function getLast(){
		return $this->last;
	}

	/**
	 * Возвращает номер предыдущей страницы
	 *
	 * @return	integer
	 */
	public function getPrevious(){
		return ($this->active-1 >= 1) ? $this->active-1 : false;
	}

	/**
	 * Возвращает номер следующей страницы
	 *
	 * @return  integer
	 */
	public function getNext(){
		return ($this->active+1 <= $this->last) ? $this->active+1 : false;
	}

	/**
	 * Возвращает список номеров страниц
	 *
	 * @return  array
	 */
	private function calculateList(){
		// список кнопок пуст или длинна не ограничена
		if ($this->last==1 || $this->list_length == 0
			|| $this->list_length > $this->last) return;

		// предварительное объявление номера первой и последней страницы
		$this->start = $this->active-floor(($this->list_length-1)/2);
		$this->end   = $this->active+ceil (($this->list_length-1)/2);
		// проверка допустимости кнопок слева
		while ($this->start < 1){
			$this->start++; // убираем лишнюю кнопку слева
			// добавляем кнопку справа, если есть
			if ($this->end+1 < $this->last) $this->end++;
		}
		// проверка допустимости кнопок справа
		while ($this->end > $this->last){
			$this->end--; // убираем лишнюю кнопку справа
			// добавляем кнопку слева, если есть
			if ($this->start-1 > 1) $this->start--;
		}
	}

	/**
	 * Возвращает список номеров страниц
	 *
	 * @return  array
	 */
	public function getList(){
		$list = array();

		// список кнопок не пуст
		if ($this->last!=1){
			// заполнение списка кнопок
			$list = array_keys(array_fill($this->start, $this->end-$this->start+1, ''));
		}
		return $list;
	}
	
}
?>