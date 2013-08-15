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
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;
use AnimeDB\Bundle\CatalogBundle\Entity\CountryTranslation;

/**
 * Country
 *
 * @ORM\Entity
 * @ORM\Table(name="country")
 * @Gedmo\TranslationEntity(class="AnimeDB\Bundle\CatalogBundle\Entity\CountryTranslation")
 * @IgnoreAnnotation("ORM")
 *
 * @package AnimeDB\Bundle\CatalogBundle\Entity
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Country implements Translatable
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
     * @Gedmo\Translatable
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
     * Entity locale
     *
     * @Gedmo\Locale
     *
     * @var string
     */
    protected $locale;

    /**
     * @ORM\OneToMany(
     *     targetEntity="CountryTranslation",
     *     mappedBy="object",
     *     cascade={"persist", "remove"}
     * )
     */
    protected $translations;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->translations = new ArrayCollection();
    }

    /**
     * Set id
     *
     * @param string $id
     *
     * @return \AnimeDB\Bundle\CatalogBundle\Entity\Country
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
     * @return \AnimeDB\Bundle\CatalogBundle\Entity\Country
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
     * @param \AnimeDB\Bundle\CatalogBundle\Entity\Item $item
     *
     * @return \AnimeDB\Bundle\CatalogBundle\Entity\Country
     */
    public function addItem(\AnimeDB\Bundle\CatalogBundle\Entity\Item $item)
    {
        if (!$this->items->contains($item)) {
            $this->items[] = $item->setManufacturer($this);
        }
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

    /**
     * Set locale
     *
     * @param string $locale
     *
     * @return \AnimeDB\Bundle\CatalogBundle\Entity\Country
     */
    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * Get translations
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getTranslations()
    {
        return $this->translations;
    }

    /**
     * Add translation
     *
     * @param \AnimeDB\Bundle\CatalogBundle\Entity\CountryTranslation $t
     *
     * @return \AnimeDB\Bundle\CatalogBundle\Entity\Country
     */
    public function addTranslation(CountryTranslation $t)
    {
        if (!$this->translations->contains($t)) {
            $this->translations[] = $t;
            $t->setObject($this);
        }
        return $this;
    }
}