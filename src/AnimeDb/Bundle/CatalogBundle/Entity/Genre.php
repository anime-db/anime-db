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
 * Genre
 *
 * @ORM\Entity
 * @ORM\Table(name="genre")
 * @IgnoreAnnotation("ORM")
 *
 * @package AnimeDb\Bundle\CatalogBundle\Entity
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Genre implements Translatable
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
     * Gender name
     *
     * @ORM\Column(type="string", length=16)
     * @Assert\NotBlank()
     * @Gedmo\Translatable
     *
     * @var string
     */
    protected $name;

    /**
     * Items list
     *
     * @ORM\ManyToMany(targetEntity="Item", mappedBy="genres")
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
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Genre
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
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Item $item
     *
     * @return Genre
     */
    public function addItem(\AnimeDb\Bundle\CatalogBundle\Entity\Item $item)
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->addGenre($this);
        }
        return $this;
    }

    /**
     * Remove items
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Item $item
     */
    public function removeItem(\AnimeDb\Bundle\CatalogBundle\Entity\Item $item)
    {
        if ($this->items->contains($item)) {
            $this->items->removeElement($item);
            $item->removeGenre($this);
        }
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
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Genre
     */
    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;
        return $this;
    }
}