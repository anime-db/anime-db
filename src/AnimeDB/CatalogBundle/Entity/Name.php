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
 * Item name
 *
 * @ORM\Entity
 * @ORM\Table(name="name")
 * @IgnoreAnnotation("ORM")
 *
 * @package AnimeDB\CatalogBundle\Entity
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Name
{
    /**
     * Id
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @var integer
     */
    protected $id;

    /**
     * Item name
     *
     * @ORM\Column(type="string", length=256)
     *
     * @var string
     */
    protected $name;

    /**
     * Items list
     *
     * @ORM\ManyToOne(targetEntity="Item", inversedBy="names")
     * @ORM\JoinColumn(name="item", referencedColumnName="id")
     *
     * @var \AnimeDB\CatalogBundle\Entity\Item
     */
    protected $item;

    /**
     * Get id
     *
     * @return integer
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
     * @return \AnimeDB\CatalogBundle\Entity\Name
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
     * Set item
     *
     * @param \AnimeDB\CatalogBundle\Entity\Item $item
     *
     * @return \AnimeDB\CatalogBundle\Entity\Name
     */
    public function setItem(\AnimeDB\CatalogBundle\Entity\Item $item = null)
    {
        $this->item = $item;
        return $this;
    }

    /**
     * Get item
     *
     * @return \AnimeDB\CatalogBundle\Entity\Item
     */
    public function getItem()
    {
        return $this->item;
    }
}