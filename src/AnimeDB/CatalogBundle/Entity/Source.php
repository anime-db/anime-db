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
 * Source for item fill
 *
 * @ORM\Entity
 * @ORM\Table(name="source")
 * @IgnoreAnnotation("ORM")
 *
 * @package AnimeDB\CatalogBundle\Entity
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Source
{
    /**
     * Id
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var integer
     */
    protected $id;

    /**
     * Source
     *
     * @ORM\Column(type="string", length=256)
     *
     * @var string
     */
    protected $source;

    /**
     * Items list
     *
     * @ORM\ManyToOne(targetEntity="Item", inversedBy="sources")
     * @ORM\JoinColumn(name="id", referencedColumnName="id")
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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set source
     *
     * @param string $source
     *
     * @return \AnimeDB\CatalogBundle\Entity\Source
     */
    public function setSource($source)
    {
        $this->source = $source;
    
        return $this;
    }

    /**
     * Get source
     *
     * @return string 
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Add items
     *
     * @param \AnimeDB\CatalogBundle\Entity\Item $items
     *
     * @return \AnimeDB\CatalogBundle\Entity\Source
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