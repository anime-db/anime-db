<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;

/**
 * Anime type
 *
 * @ORM\Entity
 * @ORM\Table(name="type")
 * @IgnoreAnnotation("ORM")
 *
 * @package AnimeDb\Bundle\CatalogBundle\Entity
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Type implements Translatable
{
    /**
     * Id
     *
     * @ORM\Id
     * @ORM\Column(type="string", length=16)
     *
     * @var integer
     */
    protected $id;

    /**
     * Type name
     *
     * @ORM\Column(type="string", length=32)
     * @Assert\NotBlank()
     * @Gedmo\Translatable
     *
     * @var string
     */
    protected $name;

    /**
     * Items list
     *
     * @ORM\OneToMany(targetEntity="Item", mappedBy="type")
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $items;

    /**
     * Entity locale
     *
     * @Gedmo\Locale
     *
     * @var string
     */
    protected $locale;

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
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Type
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
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Type
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
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Item $items
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Type
     */
    public function addItem(\AnimeDb\Bundle\CatalogBundle\Entity\Item $items)
    {
        $this->items[] = $items->setType($this);
        return $this;
    }

    /**
     * Remove item
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Item $item
     */
    public function removeItem(\AnimeDb\Bundle\CatalogBundle\Entity\Item $item)
    {
        $this->items->removeElement($item);
        $item->setType(null);
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

    /**
     * Set locale
     *
     * @param string $locale
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Type
     */
    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;
        return $this;
    }
}