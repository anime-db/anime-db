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

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use AnimeDb\Bundle\CatalogBundle\Entity\Genre;
use AnimeDb\Bundle\CatalogBundle\Entity\Country;
use AnimeDb\Bundle\CatalogBundle\Entity\Storage;
use AnimeDb\Bundle\CatalogBundle\Entity\Type;

/**
 * Item search
 *
 * @package AnimeDb\Bundle\CatalogBundle\Entity
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Search
{
    /**
     * Date end release
     *
     * @Assert\Date()
     *
     * @var \DateTime|null
     */
    protected $date_end;

    /**
     * Date start release
     *
     * @Assert\Date()
     *
     * @var \DateTime|null
     */
    protected $date_start;

    /**
     * Genre list
     *
     * @var \Doctrine\Common\Collections\ArrayCollection|null
     */
    protected $genres;

    /**
     * Manufacturer
     *
     * @var \AnimeDb\Bundle\CatalogBundle\Entity\Country|null
     */
    protected $manufacturer;

    /**
     * Main name
     *
     * @var string
     */
    protected $name;

    /**
     * Storage
     *
     * @var \AnimeDb\Bundle\CatalogBundle\Entity\Storage|null
     */
    protected $storage;

    /**
     * Type
     *
     * @var \AnimeDb\Bundle\CatalogBundle\Entity\Type|null
     */
    protected $type;

    /**
     * Construct
     */
    public function __construct() {
        $this->genres  = new ArrayCollection();
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Search
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
     * Set date_start
     *
     * @param \DateTime|null $date_start
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Search
     */
    public function setDateStart(\DateTime $date_start = null)
    {
        $this->date_start = $date_start ? clone $date_start : $date_start;
        return $this;
    }

    /**
     * Get date_start
     *
     * @return \DateTime|null
     */
    public function getDateStart()
    {
        return $this->date_start ? clone $this->date_start : null;
    }

    /**
     * Set date_end
     *
     * @param \DateTime|null $date_end
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Search
     */
    public function setDateEnd(\DateTime $date_end = null)
    {
        $this->date_end = $date_end ? clone $date_end : null;
        return $this;
    }

    /**
     * Get date_end
     *
     * @return \DateTime|null
     */
    public function getDateEnd()
    {
        return $this->date_end ? clone $this->date_end : null;
    }

    /**
     * Add genres
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Genre $genre
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Search
     */
    public function addGenre(Genre $genre)
    {
        if (!$this->genres->contains($genre)) {
            $this->genres->add($genre);
        }
        return $this;
    }

    /**
     * Remove genres
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Genre $genre
     */
    public function removeGenre(Genre $genre)
    {
        if ($this->genres->contains($genre)) {
            $this->genres->removeElement($genre);
        }
    }

    /**
     * Get genres
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGenres()
    {
        return $this->genres;
    }

    /**
     * Set manufacturer
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Country $manufacturer
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Search
     */
    public function setManufacturer(Country $manufacturer = null)
    {
        if ($this->manufacturer !== $manufacturer) {
            $this->manufacturer = $manufacturer;
        }
        return $this;
    }

    /**
     * Get manufacturer
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Country
     */
    public function getManufacturer()
    {
        return $this->manufacturer;
    }
    /**
     * Set storage
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Storage $storage
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Search
     */
    public function setStorage(Storage $storage = null)
    {
        if ($this->storage !== $storage) {
            $this->storage = $storage;
        }
        return $this;
    }

    /**
     * Get storage
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Storage
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * Set type
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Type $type
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Search
     */
    public function setType(Type $type = null)
    {
        if ($this->type !== $type) {
            $this->type = $type;
        }
        return $this;
    }

    /**
     * Get type
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Type
     */
    public function getType()
    {
        return $this->type;
    }
}