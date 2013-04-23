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
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Country
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
     * @Assert\NotBlank()
     * @Assert\Country()
     *
     * @var integer
     */
    protected $id;

    /**
     * Country name
     *
     * @ORM\Column(type="string", length=16)
     * @Assert\NotBlank()
     *
     * @var string
     */
    protected $name;

    /**
     * Items list
     *
     * @ORM\OneToMany(targetEntity="Item", mappedBy="manufacturer")
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
     * Add item
     *
     * @param \AnimeDB\CatalogBundle\Entity\Item $item
     *
     * @return \AnimeDB\CatalogBundle\Entity\Country
     */
    public function addItem(\AnimeDB\CatalogBundle\Entity\Item $item)
    {
        if (!in_array($item, $this->items)) {
            $this->items[] = $item->setManufacturer($this);
        }
        return $this;
    }

    /**
     * Remove item
     *
     * @param \AnimeDB\CatalogBundle\Entity\Item $item
     */
    public function removeItem(\AnimeDB\CatalogBundle\Entity\Item $item)
    {
        $this->items->removeElement($item);
        $item->setManufacturer(null);
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