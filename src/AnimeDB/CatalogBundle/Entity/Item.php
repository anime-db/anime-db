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
 * Item
 *
 * @ORM\Entity
 * @ORM\Table(name="item")
 * @IgnoreAnnotation("ORM")
 *
 * @package AnimeDB\CatalogBundle\Entity
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Item
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
     * Main name
     *
     * @ORM\Column(type="string", length=256)
     *
     * @var integer
     */
    protected $name;

    /**
     * Main name
     *
     * @ORM\OneToMany(targetEntity="Name", mappedBy="id")
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $names;

    /**
     * Type
     *
     * @ORM\ManyToOne(targetEntity="Type", inversedBy="items")
     * @ORM\JoinColumn(name="type", referencedColumnName="id")
     *
     * @var \AnimeDB\CatalogBundle\Entity\Type
     */
    protected $type;

    /**
     * Date start release
     *
     * @ORM\Column(type="date")
     *
     * @var DateTime
     */
    protected $date_start;

    /**
     * Date end release
     *
     * @ORM\Column(type="date")
     *
     * @var DateTime|null
     */
    protected $date_end;

    /**
     * Genre list
     *
     * @ORM\ManyToMany(targetEntity="Genre", inversedBy="items")
     *
     * @var \AnimeDB\CatalogBundle\Entity\Genre
     */
    protected $genres;

    /**
     * Country of origin
     *
     * @ORM\ManyToOne(targetEntity="Country", inversedBy="items")
     * @ORM\JoinColumn(name="production", referencedColumnName="id")
     *
     * @var \AnimeDB\CatalogBundle\Entity\Country
     */
    protected $production;

    /**
     * Duration
     *
     * @ORM\Column(type="integer")
     *
     * @var integer
     */
    protected $duration;

    /**
     * Summary
     *
     * @ORM\Column(type="text")
     *
     * @var string
     */
    protected $summary;

    /**
     * Disk path
     *
     * @ORM\Column(type="string", length=256)
     *
     * @var string
     */
    protected $path;

    /**
     * Storage
     *
     * @ORM\ManyToOne(targetEntity="Storage", inversedBy="items")
     * @ORM\JoinColumn(name="storage", referencedColumnName="id")
     *
     * @var integer
     */
    protected $storage;

    /**
     * Episodes list
     *
     * @ORM\Column(type="text")
     *
     * @var string
     */
    protected $episodes;

    /**
     * Translate (subtitles and voice)
     *
     * @ORM\Column(type="string", length=256)
     *
     * @var string
     */
    protected $translate;

    /**
     * File info
     *
     * @ORM\Column(type="text")
     *
     * @var string
     */
    protected $file_info;

    /**
     * Source list
     *
     * @ORM\OneToMany(targetEntity="Source", mappedBy="id")
     *
     * @var \AnimeDB\CatalogBundle\Entity\Source
     */
    protected $sources;

    /**
     * Image
     *
     * @ORM\Column(type="string", length=256)
     *
     * @var string
     */
    protected $image;

    /**
     * Image list
     *
     *
     * @ORM\OneToMany(targetEntity="Image", mappedBy="id")
     *
     * @var \AnimeDB\CatalogBundle\Entity\Image
     */
    protected $images;

    /**
     * Construct
     */
    public function __construct() {
        $this->genres  = new ArrayCollection();
        $this->names   = new ArrayCollection();
        $this->sources = new ArrayCollection();
        $this->images  = new ArrayCollection();
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
     * @return \AnimeDB\CatalogBundle\Entity\Item
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
     * @param \DateTime $dateStart
     *
     * @return \AnimeDB\CatalogBundle\Entity\Item
     */
    public function setDateStart($dateStart)
    {
        $this->date_start = $dateStart;
        return $this;
    }

    /**
     * Get date_start
     *
     * @return \DateTime
     */
    public function getDateStart()
    {
        return $this->date_start;
    }

    /**
     * Set date_end
     *
     * @param \DateTime $dateEnd
     *
     * @return \AnimeDB\CatalogBundle\Entity\Item
     */
    public function setDateEnd($dateEnd)
    {
        $this->date_end = $dateEnd;
        return $this;
    }

    /**
     * Get date_end
     *
     * @return \DateTime
     */
    public function getDateEnd()
    {
        return $this->date_end;
    }

    /**
     * Set duration
     *
     * @param integer $duration
     *
     * @return \AnimeDB\CatalogBundle\Entity\Item
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
        return $this;
    }

    /**
     * Get duration
     *
     * @return integer
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Set summary
     *
     * @param string $summary
     *
     * @return \AnimeDB\CatalogBundle\Entity\Item
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;
        return $this;
    }

    /**
     * Get summary
     *
     * @return string
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * Set path
     *
     * @param string $path
     *
     * @return \AnimeDB\CatalogBundle\Entity\Item
     */
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set episodes
     *
     * @param string $episodes
     *
     * @return \AnimeDB\CatalogBundle\Entity\Item
     */
    public function setEpisodes($episodes)
    {
        $this->episodes = $episodes;
        return $this;
    }

    /**
     * Get episodes
     *
     * @return string
     */
    public function getEpisodes()
    {
        return $this->episodes;
    }

    /**
     * Set translate
     *
     * @param string $translate
     *
     * @return \AnimeDB\CatalogBundle\Entity\Item
     */
    public function setTranslate($translate)
    {
        $this->translate = $translate;
        return $this;
    }

    /**
     * Get translate
     *
     * @return string
     */
    public function getTranslate()
    {
        return $this->translate;
    }

    /**
     * Set file info
     *
     * @param string $fileInfo
     *
     * @return \AnimeDB\CatalogBundle\Entity\Item
     */
    public function setFileInfo($fileInfo)
    {
        $this->file_info = $fileInfo;
        return $this;
    }

    /**
     * Get file_info
     *
     * @return string
     */
    public function getFileInfo()
    {
        return $this->file_info;
    }

    /**
     * Add names
     *
     * @param \AnimeDB\CatalogBundle\Entity\Item $names
     *
     * @return \AnimeDB\CatalogBundle\Entity\Item
     */
    public function addName(\AnimeDB\CatalogBundle\Entity\Item $names)
    {
        $this->names[] = $names;
        return $this;
    }

    /**
     * Remove names
     *
     * @param \AnimeDB\CatalogBundle\Entity\Item $names
     */
    public function removeName(\AnimeDB\CatalogBundle\Entity\Item $names)
    {
        $this->names->removeElement($names);
    }

    /**
     * Get names
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNames()
    {
        return $this->names;
    }

    /**
     * Set type
     *
     * @param \AnimeDB\CatalogBundle\Entity\Type $type
     *
     * @return \AnimeDB\CatalogBundle\Entity\Item
     */
    public function setType(\AnimeDB\CatalogBundle\Entity\Type $type = null)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get type
     *
     * @return \AnimeDB\CatalogBundle\Entity\Type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Add genres
     *
     * @param \AnimeDB\CatalogBundle\Entity\Genre $genres
     *
     * @return \AnimeDB\CatalogBundle\Entity\Item
     */
    public function addGenre(\AnimeDB\CatalogBundle\Entity\Genre $genres)
    {
        $this->genres[] = $genres;
        return $this;
    }

    /**
     * Remove genres
     *
     * @param \AnimeDB\CatalogBundle\Entity\Genre $genres
     */
    public function removeGenre(\AnimeDB\CatalogBundle\Entity\Genre $genres)
    {
        $this->genres->removeElement($genres);
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
     * Set production
     *
     * @param \AnimeDB\CatalogBundle\Entity\Country $production
     *
     * @return \AnimeDB\CatalogBundle\Entity\Item
     */
    public function setProduction(\AnimeDB\CatalogBundle\Entity\Country $production = null)
    {
        $this->production = $production;
        return $this;
    }

    /**
     * Get production
     *
     * @return \AnimeDB\CatalogBundle\Entity\Country
     */
    public function getProduction()
    {
        return $this->production;
    }

    /**
     * Set storage
     *
     * @param \AnimeDB\CatalogBundle\Entity\Storage $storage
     *
     * @return \AnimeDB\CatalogBundle\Entity\Item
     */
    public function setStorage(\AnimeDB\CatalogBundle\Entity\Storage $storage = null)
    {
        $this->storage = $storage;
        return $this;
    }

    /**
     * Get storage
     *
     * @return \AnimeDB\CatalogBundle\Entity\Storage
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * Set image
     *
     * @param string $image
     *
     * @return \AnimeDB\CatalogBundle\Entity\Item
     */
    public function setImage($image)
    {
        $this->image = $image;
        return $this;
    }

    /**
     * Get image
     *
     * @return string 
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Add sources
     *
     * @param \AnimeDB\CatalogBundle\Entity\Source $sources
     *
     * @return \AnimeDB\CatalogBundle\Entity\Item
     */
    public function addSource(\AnimeDB\CatalogBundle\Entity\Source $sources)
    {
        $this->sources[] = $sources;
        return $this;
    }

    /**
     * Remove sources
     *
     * @param \AnimeDB\CatalogBundle\Entity\Source $sources
     */
    public function removeSource(\AnimeDB\CatalogBundle\Entity\Source $sources)
    {
        $this->sources->removeElement($sources);
    }

    /**
     * Get sources
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSources()
    {
        return $this->sources;
    }

    /**
     * Add images
     *
     * @param \AnimeDB\CatalogBundle\Entity\Image $images
     *
     * @return \AnimeDB\CatalogBundle\Entity\Item
     */
    public function addImage(\AnimeDB\CatalogBundle\Entity\Image $images)
    {
        $this->images[] = $images;
        return $this;
    }

    /**
     * Remove images
     *
     * @param \AnimeDB\CatalogBundle\Entity\Image $images
     */
    public function removeImage(\AnimeDB\CatalogBundle\Entity\Image $images)
    {
        $this->images->removeElement($images);
    }

    /**
     * Get images
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getImages()
    {
        return $this->images;
    }
}