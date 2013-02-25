<?php
namespace stdlib;

/**
 * Interface data caching
 * 
 * @package	Standard Library
 * @author	Peter Gribanov
 * @since	02.12.10
 * @version	1.1
 */
class Cache {

	/**
	 * Directory to store cache files
	 * 
	 * @var	string
	 */
	protected $cache_dir;

	/**
	 * Unlimited in life cache
	 * 
	 * @var	string
	 */
	protected $no_expired;


	/**
	 * Constructor
	 *
	 * @param	string
	 * @param	string
	 * @return	void
	 */
	public function __construct($cache_dir, $no_expired=false){
		if (!is_string($cache_dir)){
			throw InvalidArgumentException('Incorrect directory to store cache files');
		}
		if (!is_dir($cache_dir)){
			throw new InvalidArgumentException('Directory to store cache files is not found');
		}
		if (!is_bool($no_expired)){
			throw new InvalidArgumentException('Incorrect expired flag');
		}
		$this->cache_dir = $cache_dir;
		$this->no_expired = $no_expired;
	}

	/**
	 * Stores a dataset
	 * 
	 * @param	string  dataset ID
	 * @param	mixid	dataset value
	 * @param	integer	maximum age timestamp
	 * @return	boolen
	 */
	public function save($id, $cachedata, $expires){
		if (!is_string($id) || !trim($id)){
			throw new InvalidArgumentException('Invalid id of dataset');
		}
		if (!is_integer($expires) || $expires<0){
			throw new InvalidArgumentException('Invalid cache lifetime');
		}
		$file = $this->getFilename($id);
		$put = (bool)file_put_contents($file, serialize($cachedata));
		// life extension file
		if ($expires) touch($file, time()+$expires);
		return $put;
	}

	/**
	 * Remove dataset
	 * 
	 * @param	string  dataset ID
	 * @return	boolean
	 */
	public function remove($id){
		$file = $this->getFilename($id);
		return file_exists($file) ? unlink($file) : false;
	}

	/**
	 * Loads a dataset from the cache.
	 * 
	 * @param	string	dataset ID
	 * @return	mixed	dataset value or null on failure
	 */
	public function load($id){
		$file = $this->getFilename($id);
		if (!file_exists($file)) return null;
		return unserialize(file_get_contents($file));
	}

	/**
	 * Checks if a dataset is expired.
	 * 
	 * @param	string	dataset ID
	 * @return	boolean
	 */
	public function isExpired($id){
		// check if at all it is cached
		if (!$this->isCached($id)) return true;
		if ($this->no_expired) return false;

		$expires = filemtime($this->getFilename($id));
		if ($expired=($expires <= time())){
			$this->remove($id);
		}
		return $expired;
	}

	/**
	 * Checks if a dataset is cached.
	 * 
	 * @param	string	dataset ID
	 * @return	boolean
	 */
	public function isCached($id){
		return $this->idExists($id);
	}

	/**
	 * Checks if a id data is exists.
	 * 
	 * @param	string	dataset ID
	 * @return	boolean
	 */
	public function idExists($id){
		return file_exists($this->getFilename($id));
	}

	/**
	 * Generates a "unique" ID for the given value
	 * 
	 * This is a quick but dirty hack to get a "unique" ID for a any kind of variable.
	 * ID clashes might occur from time to time although they are extreme unlikely!
	 * 
	 * @param	mixed	variable to generate a ID for
	 * @return	string	"unique" ID
	 */
	public function generateID($variable){
		return md5(serialize($variable));
	}

	/**
	 * Returns the filename for the specified id.
	 * 
	 * @param	string	dataset ID
	 * @return	string	full filename with the path
	 */
	public function getFilename($id){
		return $this->cache_dir.$id.'.cache';
	}

}
?>