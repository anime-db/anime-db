<?php
/**
 * AnimeDB package
 *
 * @package   AnimeDB
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDB\Bundle\CatalogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Storage of item files
 *
 * @ORM\Entity
 * @ORM\Table(name="storage")
 * @IgnoreAnnotation("ORM")
 *
 * @package AnimeDB\Bundle\CatalogBundle\Entity
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Storage
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
     * Storage name
     *
     * @ORM\Column(type="string", length=128)
     * @Assert\NotBlank()
     *
     * @var string
     */
    protected $name;

    /**
     * Storage description
     *
     * @ORM\Column(type="text")
     *
     * @var string
     */
    protected $description;

    /**
     * Items list
     *
     * @ORM\OneToMany(targetEntity="Item", mappedBy="storage")
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
     * Set name
     *
     * @param string $name
     *
     * @return \AnimeDB\Bundle\CatalogBundle\Entity\Storage
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
     * Set description
     *
     * @param string $description
     *
     * @return \AnimeDB\Bundle\CatalogBundle\Entity\Storage
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Add item
     *
     * @param \AnimeDB\Bundle\CatalogBundle\Entity\Item $item
     *
     * @return \AnimeDB\Bundle\CatalogBundle\Entity\Storage
     */
    public function addItem(\AnimeDB\Bundle\CatalogBundle\Entity\Item $item)
    {
        $this->items[] = $item->setStorage($this);
        return $this;
    }

    /**
     * Remove item
     *
     * @param \AnimeDB\Bundle\CatalogBundle\Entity\Item $item
     */
    public function removeItem(\AnimeDB\Bundle\CatalogBundle\Entity\Item $item)
    {
        $this->items->removeElement($item);
        $item->setStorage(null);
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