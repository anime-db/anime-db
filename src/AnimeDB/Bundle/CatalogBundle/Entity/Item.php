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
use AnimeDB\Bundle\CatalogBundle\Entity\Country;
use AnimeDB\Bundle\CatalogBundle\Entity\Storage;
use AnimeDB\Bundle\CatalogBundle\Entity\Type;
use Doctrine\ORM\EntityManager;

/**
 * Item
 *
 * @ORM\Entity
 * @ORM\Table(name="item")
 * @ORM\HasLifecycleCallbacks
 * @IgnoreAnnotation("ORM")
 *
 * @package AnimeDB\Bundle\CatalogBundle\Entity
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
    protected $name = '';

    /**
     * Main name
     *
     * @ORM\OneToMany(targetEntity="Name", mappedBy="item", cascade={"persist", "remove"}, orphanRemoval=true)
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $names;

    /**
     * Type
     *
     * @ORM\ManyToOne(targetEntity="Type", inversedBy="items", cascade={"persist"})
     * @ORM\JoinColumn(name="type", referencedColumnName="id")
     *
     * @var \AnimeDB\Bundle\CatalogBundle\Entity\Type
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
     * @ORM\Column(type="date", nullable=true)
     * @Assert\Date()
     *
     * @var DateTime|null
     */
    protected $date_end;

    /**
     * Genre list
     *
     * @ORM\ManyToMany(targetEntity="Genre", inversedBy="items", cascade={"persist"})
     * @ORM\JoinTable(name="items_genres")
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $genres;

    /**
     * Manufacturer
     *
     * @ORM\ManyToOne(targetEntity="Country", inversedBy="items", cascade={"persist"})
     * @ORM\JoinColumn(name="manufacturer", referencedColumnName="id")
     *
     * @var \AnimeDB\Bundle\CatalogBundle\Entity\Country
     */
    protected $manufacturer;

    /**
     * Duration
     *
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Type(type="integer", message="The value {{ value }} is not a valid {{ type }}.")
     *
     * @var integer
     */
    protected $duration = 0;

    /**
     * Summary
     *
     * @ORM\Column(type="text", nullable=true)
     *
     * @var string
     */
    protected $summary = '';

    /**
     * Disk path
     *
     * @ORM\Column(type="string", length=256)
     * @Assert\NotBlank()
     *
     * @var string
     */
    protected $path = '';

    /**
     * Storage
     *
     * @ORM\ManyToOne(targetEntity="Storage", inversedBy="items", cascade={"persist"})
     * @ORM\JoinColumn(name="storage", referencedColumnName="id")
     *
     * @var integer
     */
    protected $storage;

    /**
     * Episodes list
     *
     * @ORM\Column(type="text", nullable=true)
     *
     * @var string
     */
    protected $episodes = '';

    /**
     * Translate (subtitles and voice)
     *
     * @ORM\Column(type="string", length=256, nullable=true)
     *
     * @var string
     */
    protected $translate = '';

    /**
     * File info
     *
     * @ORM\Column(type="text", nullable=true)
     *
     * @var string
     */
    protected $file_info = '';

    /**
     * Source list
     *
     * @ORM\OneToMany(targetEntity="Source", mappedBy="item", cascade={"persist", "remove"}, orphanRemoval=true)
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $sources;

    /**
     * Cover
     *
     * @ORM\Column(type="string", length=256, nullable=true)
     *
     * @var string
     */
    protected $cover = '';

    /**
     * Image list
     *
     * @ORM\OneToMany(targetEntity="Image", mappedBy="item", cascade={"persist", "remove"}, orphanRemoval=true)
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $images;

    /**
     * Old covers list
     *
     * @var array
     */
    protected $old_covers = [];

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
     * @return \AnimeDB\Bundle\CatalogBundle\Entity\Item
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
     * @return \AnimeDB\Bundle\CatalogBundle\Entity\Item
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
     * @return \AnimeDB\Bundle\CatalogBundle\Entity\Item
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
     * @return \AnimeDB\Bundle\CatalogBundle\Entity\Item
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
     * @return \AnimeDB\Bundle\CatalogBundle\Entity\Item
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
     * @return \AnimeDB\Bundle\CatalogBundle\Entity\Item
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
     * @return \AnimeDB\Bundle\CatalogBundle\Entity\Item
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
     * @return \AnimeDB\Bundle\CatalogBundle\Entity\Item
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
     * @return \AnimeDB\Bundle\CatalogBundle\Entity\Item
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
     * @param \AnimeDB\Bundle\CatalogBundle\Entity\Name $name
     *
     * @return \AnimeDB\Bundle\CatalogBundle\Entity\Item
     */
    public function addName(\AnimeDB\Bundle\CatalogBundle\Entity\Name $name)
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
     * @param \AnimeDB\Bundle\CatalogBundle\Entity\Name $name
     */
    public function removeName(\AnimeDB\Bundle\CatalogBundle\Entity\Name $name)
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
     * @param \AnimeDB\Bundle\CatalogBundle\Entity\Type $type
     *
     * @return \AnimeDB\Bundle\CatalogBundle\Entity\Item
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
     * @return \AnimeDB\Bundle\CatalogBundle\Entity\Type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Add genres
     *
     * @param \AnimeDB\Bundle\CatalogBundle\Entity\Genre $genre
     *
     * @return \AnimeDB\Bundle\CatalogBundle\Entity\Item
     */
    public function addGenre(\AnimeDB\Bundle\CatalogBundle\Entity\Genre $genre)
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
     * @param \AnimeDB\Bundle\CatalogBundle\Entity\Genre $genre
     */
    public function removeGenre(\AnimeDB\Bundle\CatalogBundle\Entity\Genre $genre)
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
     * @param \AnimeDB\Bundle\CatalogBundle\Entity\Country $manufacturer
     *
     * @return \AnimeDB\Bundle\CatalogBundle\Entity\Item
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
     * @return \AnimeDB\Bundle\CatalogBundle\Entity\Country
     */
    public function getManufacturer()
    {
        return $this->manufacturer;
    }

    /**
     * Set storage
     *
     * @param \AnimeDB\Bundle\CatalogBundle\Entity\Storage $storage
     *
     * @return \AnimeDB\Bundle\CatalogBundle\Entity\Item
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
     * @return \AnimeDB\Bundle\CatalogBundle\Entity\Storage
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * Set cover
     *
     * @param string $cover
     *
     * @return \AnimeDB\Bundle\CatalogBundle\Entity\Item
     */
    public function setCover($cover)
    {
        // copy current cover to old for remove old cover file after update
        if ($this->cover) {
            $this->old_covers[] = $this->cover;
        }
        $this->cover = $cover;
        return $this;
    }

    /**
     * Get cover
     *
     * @return string 
     */
    public function getCover()
    {
        return $this->cover;
    }

    /**
     * Add source
     *
     * @param \AnimeDB\Bundle\CatalogBundle\Entity\Source $source
     *
     * @return \AnimeDB\Bundle\CatalogBundle\Entity\Item
     */
    public function addSource(\AnimeDB\Bundle\CatalogBundle\Entity\Source $source)
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
     * @param \AnimeDB\Bundle\CatalogBundle\Entity\Source $source
     */
    public function removeSource(\AnimeDB\Bundle\CatalogBundle\Entity\Source $source)
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
     * @param \AnimeDB\Bundle\CatalogBundle\Entity\Image $image
     *
     * @return \AnimeDB\Bundle\CatalogBundle\Entity\Item
     */
    public function addImage(\AnimeDB\Bundle\CatalogBundle\Entity\Image $image)
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
     * @param \AnimeDB\Bundle\CatalogBundle\Entity\Image $image
     */
    public function removeImage(\AnimeDB\Bundle\CatalogBundle\Entity\Image $image)
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

    /**
     * Remove cover file
     *
     * @ORM\PostRemove
     */
    public function doRemoveCover()
    {
        if ($this->cover && file_exists($this->getAbsolutePath())) {
            unlink($this->getAbsolutePath());
        }
    }

    /**
     * Remove old cover files
     *
     * @ORM\PostRemove
     * @ORM\PostUpdate
     */
    public function doRemoveOldCovers()
    {
        while ($cover = array_shift($this->old_covers)) {
            if (file_exists($this->getUploadRootDir().'/'.$cover)) {
                unlink($this->getUploadRootDir().'/'.$cover);
            }
        }
    }

    /**
     * Get absolute path
     *
     * @return string
     */
    public function getAbsolutePath()
    {
        return $this->cover !== null ? $this->getUploadRootDir().'/'.$this->cover : null;
    }

    /**
     * Get upload root dir
     *
     * @return string
     */
    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../../../web/'.$this->getUploadDir();
    }

    /**
     * Get upload dir
     *
     * @return string
     */
    protected function getUploadDir()
    {
        return 'media';
    }
}