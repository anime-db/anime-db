<?php
namespace imagemanager;

/**
 * Class Pixel
 *
 * Used
 *  $px = new Pixel();
 *  $px->setColor('Cyanogen');
 *  $px->getColorAsString();
 * 
 *  Pixel::create('#cc00ff')->getColor();
 * 
 * @package	Image Manager
 * @author	Peter Gribanov
 * @since	18.11.2010
 * @version	1.2
 */
class Pixel {

	/**
	 * Выбранный цвет
	 * По умолчанию черный
	 * 
	 * @var	array
	 */
	private $color = array(0,0,0);


	/**
	 * Возвращает экземпляр класса
	 * 
	 * @param	string
	 * @return	Pixel
	 */
	public static function create($color){
		$p = new self();
		return $p->setColor($color);
	}

	/**
	 * Возвращает текущий показатель цвета
	 * 
	 * @return	array
	 */
	public function getColor(){
		return $this->color;
	}

	/**
	 * Возвращает текущий показатель цвета в виде строки
	 * 
	 * @return	string
	 */
	public function getColorAsString(){
		return implode(',', $this->color);
	}

	/**
	 * Устанавливает цвет
	 * 
	 * Понимает названия основных цветов(blue, indigo)
	 * а так же значения вида:
	 *  #0000ff
	 *  #00f
	 *  rgb(0,0,255)
	 *  cmyk(100,100,100,10)
	 * 
	 * @param	string
	 * @return	boolen
	 */
	public function setColor($color){
		if (!is_string($color) || !trim($color)) return false;

		$color = strtolower($color);

		$color_names = array(
			'aqua'			=> array(0,255,255),
			'aquamarine'	=> array(127,255,212),
			'azure'			=> array(240,255,255),
			'beige'			=> array(245,245,220),
			'bisque'		=> array(255,228,196),
			'black'			=> array(0,0,0),
			'blue'			=> array(0,0,255),
			'brown'			=> array(165,42,42),
			'chartreuse'	=> array(127,255,0),
			'chocolate'		=> array(210,105,30),
			'coral'			=> array(255,127,80),
			'cornsilk'		=> array(255,248,220),
			'crimson'		=> array(237,164,61),
			'cyan'			=> array(0,255,255),
			'darkorange'	=> array(255,140,0),
			'fuchsia'		=> array(255,0,255),
			'gainsboro'		=> array(220,220,220),
			'gold'			=> array(255,215,0),
			'gray'			=> array(128,128,128),
			'green'			=> array(0,128,0),
			'indigo'		=> array(75,0,130),
			'ivory'			=> array(255,255,240),
			'khaki'			=> array(240,230,140),
			'lavender'		=> array(230,230,250),
			'lime'			=> array(0,255,0),
			'linen'			=> array(250,240,230),
			'magenta'		=> array(255,0,255),
			'maroon'		=> array(128,0,0),
			'moccasin'		=> array(255,228,181),
			'navy'			=> array(0,0,128),
			'olive'			=> array(128,128,0),
			'orange'		=> array(255,165,0),
			'orchid'		=> array(218,112,214),
			'peru'			=> array(205,133,63),
			'pink'			=> array(255,192,203),
			'plum'			=> array(221,160,221),
			'purple'		=> array(128,0,128),
			'red'			=> array(255,0,0),
			'salmon'		=> array(250,128,114),
			'sienna'		=> array(160,82,45),
			'silver'		=> array(192,192,192),
			'snow'			=> array(255,250,250),
			'tan'			=> array(210,180,140),
			'teal'			=> array(0,128,128),
			'thistle'		=> array(216,191,216),
			'tomato'		=> array(255,99,71),
			'turquoise'		=> array(64,224,208),
			'violet'		=> array(238,130,238),
			'wheat'			=> array(245,222,179),
			'white'			=> array(255,255,255),
			'yellow'		=> array(255,255,0),
		);

		// color names - blue
		if (isset($color_names[$color])){
			$this->color = $color_names[$color];
			return true;
		}
		// hex #0000ff
		if (preg_match('/^#([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})$/', $color, $mat)){
			$this->color = array(hexdec($mat[1]), hexdec($mat[2]), hexdec($mat[3]));
			return true;
		}
		// hex #00f
		if (preg_match('/^#([0-9a-f]{1})([0-9a-f]{1})([0-9a-f]{1})$/', $color, $mat)){
			$this->color = array(hexdec($mat[1].$mat[1]), hexdec($mat[2].$mat[2]), hexdec($mat[3].$mat[3]));
			return true;
		}

		$color = str_replace(' ', '', $color);

		// rgb(0,0,255)
		if (preg_match('/^rgb\(([0-9]{1,3}),([0-9]{1,3}),([0-9]{1,3})\)$/', $color, $mat)){
			$this->color = array($mat[1], $mat[2], $mat[3]);
			return true;
		}
		// cmyk(100,100,100,10)
		if (preg_match('/^cmyk\(([0-9]{1,3}),([0-9]{1,3}),([0-9]{1,3}),([0-9]{1,3})\)$/', $color, $mat)){
			$c = (255 * $mat[1]) / 100;
			$m = (255 * $mat[2]) / 100;
			$y = (255 * $mat[3]) / 100;
			$k = (255 * $mat[4]) / 100;

			$this->color = array(
				round((255 - $c) * (255 - $k) / 255),
				round((255 - $m) * (255 - $k) / 255),
				round((255 - $y) * (255 - $k) / 255)
			);
			return true;
		}
		return false;
	}

	/**
	 * Очищает текущий цвет
	 * 
	 * @return	void
	 */
	public function clear(){
		unset($this->color);
		$this->color = array(0,0,0);
	}

}
?>