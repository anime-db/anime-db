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
use AnimeDB\CatalogBundle\Entity\Country;
use AnimeDB\CatalogBundle\Entity\Storage;
use AnimeDB\CatalogBundle\Entity\Type;

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
     * @Assert\NotBlank()
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
     * @Assert\Date()
     *
     * @var DateTime
     */
    protected $date_start;

    /**
     * Date end release
     *
     * @ORM\Column(type="date")
     * @Assert\Date()
     *
     * @var DateTime|null
     */
    protected $date_end;

    /**
     * Genre list
     *
     * @ORM\ManyToMany(targetEntity="Genre", inversedBy="items")
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $genres;

    /**
     * Manufacturer
     *
     * @ORM\ManyToOne(targetEntity="Country", inversedBy="items")
     * @ORM\JoinColumn(name="manufacturer", referencedColumnName="id")
     *
     * @var \AnimeDB\CatalogBundle\Entity\Country
     */
    protected $manufacturer;

    /**
     * Duration
     *
     * @ORM\Column(type="integer")
     * @Assert\Type(type="integer", message="The value {{ value }} is not a valid {{ type }}.")
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
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $sources;

    /**
     * Image
     *
     * @ORM\Column(type="string", length=256)
     * @Assert\Image()
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
     * @var \Doctrine\Common\Collections\ArrayCollection
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
     * @param \DateTime|null $dateStart
     *
     * @return \AnimeDB\CatalogBundle\Entity\Item
     */
    public function setDateStart(\DateTime $dateStart = null)
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
     * @param \DateTime|null $dateEnd
     *
     * @return \AnimeDB\CatalogBundle\Entity\Item
     */
    public function setDateEnd(\DateTime $dateEnd = null)
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
     * Add name
     *
     * @param \AnimeDB\CatalogBundle\Entity\Name $name
     *
     * @return \AnimeDB\CatalogBundle\Entity\Item
     */
    public function addName(\AnimeDB\CatalogBundle\Entity\Name $name)
    {
        if (!$this->names->contains($name)) {
            $this->names->add($name);
            $name->setItem($this);
        }
        return $this;
    }

    /**
     * Remove name
     *
     * @param \AnimeDB\CatalogBundle\Entity\Name $name
     */
    public function removeName(\AnimeDB\CatalogBundle\Entity\Name $name)
    {
        if ($this->names->contains($name)) {
            $this->names->removeElement($name);
            $name->setItem(null);
        }
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
    public function setType(Type $type = null)
    {
        if ($this->type !== $type) {
            // romove link on this item for old type
            if ($this->type instanceof Type) {
                $tmp = $this->type;
                $this->type = null;
                $tmp->removeItem($this);
            }
            $this->type = $type;
            // add link on this item
            if ($this->type instanceof Type) {
                $this->type->addItem($this);
            }
        }
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
     * @param \AnimeDB\CatalogBundle\Entity\Genre $genre
     *
     * @return \AnimeDB\CatalogBundle\Entity\Item
     */
    public function addGenre(\AnimeDB\CatalogBundle\Entity\Genre $genre)
    {
        if (!$this->genres->contains($genre)) {
            $this->genres->add($genre);
            $genre->addItem($this);
        }
        return $this;
    }

    /**
     * Remove genres
     *
     * @param \AnimeDB\CatalogBundle\Entity\Genre $genre
     */
    public function removeGenre(\AnimeDB\CatalogBundle\Entity\Genre $genre)
    {
        if ($this->genres->contains($genre)) {
            $this->genres->removeElement($genre);
            $genre->removeItem($this);
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
     * @param \AnimeDB\CatalogBundle\Entity\Country $manufacturer
     *
     * @return \AnimeDB\CatalogBundle\Entity\Item
     */
    public function setManufacturer(Country $manufacturer = null)
    {
        if ($this->manufacturer !== $manufacturer) {
            // romove link on this item for old manufacturer
            if ($this->manufacturer instanceof Country) {
                $tmp = $this->manufacturer;
                $this->manufacturer = null;
                $tmp->removeItem($this);
            }
            $this->manufacturer = $manufacturer;
            // add link on this item
            if ($this->manufacturer instanceof Country) {
                $this->manufacturer->addItem($this);
            }
        }
        return $this;
    }

    /**
     * Get manufacturer
     *
     * @return \AnimeDB\CatalogBundle\Entity\Country
     */
    public function getManufacturer()
    {
        return $this->manufacturer;
    }

    /**
     * Set storage
     *
     * @param \AnimeDB\CatalogBundle\Entity\Storage $storage
     *
     * @return \AnimeDB\CatalogBundle\Entity\Item
     */
    public function setStorage(Storage $storage = null)
    {
        if ($this->storage !== $storage) {
            // romove link on this item for old storage
            if ($this->storage instanceof Storage) {
                $tmp = $this->storage;
                $this->storage = null;
                $tmp->removeItem($this);
            }
            $this->storage = $storage;
            // add link on this item
            if ($this->storage instanceof Storage) {
                $this->storage->addItem($this);
            }
        }
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
     * Add source
     *
     * @param \AnimeDB\CatalogBundle\Entity\Source $source
     *
     * @return \AnimeDB\CatalogBundle\Entity\Item
     */
    public function addSource(\AnimeDB\CatalogBundle\Entity\Source $source)
    {
        if (!$this->sources->contains($source)) {
            $this->sources->add($source);
            $source->setItem($this);
        }
        return $this;
    }

    /**
     * Remove source
     *
     * @param \AnimeDB\CatalogBundle\Entity\Source $source
     */
    public function removeSource(\AnimeDB\CatalogBundle\Entity\Source $source)
    {
        if ($this->sources->contains($source)) {
            $this->sources->removeElement($source);
            $source->setItem(null);
        }
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
     * Add image
     *
     * @param \AnimeDB\CatalogBundle\Entity\Image $image
     *
     * @return \AnimeDB\CatalogBundle\Entity\Item
     */
    public function addImage(\AnimeDB\CatalogBundle\Entity\Image $image)
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setItem($this);
        }
        return $this;
    }

    /**
     * Remove image
     *
     * @param \AnimeDB\CatalogBundle\Entity\Image $image
     */
    public function removeImage(\AnimeDB\CatalogBundle\Entity\Image $image)
    {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
            $image->setItem(null);
        }
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