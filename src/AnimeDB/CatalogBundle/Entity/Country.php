<?php
/**
 * AnimeDB package
 *
 * @package   AnimeDB
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDB\CatalogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Страна
 *
 * @ORM\Entity
 * @ORM\Table(name="country")
 * @IgnoreAnnotation("ORM")
 *
 * @package AnimeDB\CatalogBundle\Entity
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Country
{

	/**
	 * Id
	 *
	 * @ORM\Id
	 * @ORM\Column(type="string", length=2)
	 *
	 * @var integer
	 */
	protected $id;

	/**
	 * Country name
	 *
	 * @ORM\Column(type="string", length=16)
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * Items list
	 *
	 * @ORM\OneToMany(targetEntity="Item", mappedBy="production")
	 *
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $items;


	/**
	 * Construct
	 */
	public function __construct()
	{
		$this->items = new ArrayCollection();
	}


	/**
	 * Set id
	 *
	 * @param string $id
	 *
	 * @return \AnimeDB\CatalogBundle\Entity\Country
	 */
	public function setId($id)
	{
		$this->id = $id;
		return $this;
	}

	/**
	 * Get id
	 *
	 * @return string
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Set name
	 *
	 * @param string $name
	 *
	 * @return \AnimeDB\CatalogBundle\Entity\Country
	 */
	public function setName($name)
	{
		$this->name = $name;
		return $this;
	}

	/**
	 * Get name
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Add items
	 *
	 * @param \AnimeDB\CatalogBundle\Entity\Item $items
	 *
	 * @return \AnimeDB\CatalogBundle\Entity\Country
	 */
	public function addItem(\AnimeDB\CatalogBundle\Entity\Item $items)
	{
		$this->items[] = $items;
		return $this;
	}

	/**
	 * Remove items
	 *
	 * @param \AnimeDB\CatalogBundle\Entity\Item $items
	 */
	public function removeItem(\AnimeDB\CatalogBundle\Entity\Item $items)
	{
		$this->items->removeElement($items);
	}

	/**
	 * Get items
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getItems()
	{
		return $this->items;
	}

}