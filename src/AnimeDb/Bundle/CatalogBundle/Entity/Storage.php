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
use Symfony\Component\Validator\ExecutionContextInterface;

/**
 * Storage of item files
 *
 * @ORM\Entity
 * @ORM\Table(name="storage")
 * @Assert\Callback(methods={"isPathValid"})
 * @ORM\Entity(repositoryClass="AnimeDb\Bundle\CatalogBundle\Repository\Storage")
 * @IgnoreAnnotation("ORM")
 *
 * @package AnimeDb\Bundle\CatalogBundle\Entity
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Storage
{
    /**
     * Type folder on computer (local/network)
     *
     * @var string
     */
    const TYPE_FOLDER = 'folder';

    /**
     * Type external storage (HDD/Flash/SD)
     *
     * @var string
     */
    const TYPE_EXTERNAL = 'external';

    /**
     * Type external storage read-only (CD/DVD)
     *
     * @var string
     */
    const TYPE_EXTERNAL_R = 'external-r';

    /**
     * Type video storage (DVD/BD/VHS)
     *
     * @var string
     */
    const TYPE_VIDEO = 'video';

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
     * Type
     *
     * @ORM\Column(type="string", length=16)
     * @Assert\Choice(callback = "getTypes")
     *
     * @var string
     */
    protected $type;

    /**
     * Path on computer
     *
     * @ORM\Column(type="text", nullable=true)
     *
     * @var string
     */
    protected $path;

    /**
     * Date of last modified
     *
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    protected $modified;

    /**
     * Items list
     *
     * @ORM\OneToMany(targetEntity="Item", mappedBy="storage")
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $items;

    /**
     * Type names
     *
     * @var array
     */
    public static $type_names = [
        self::TYPE_FOLDER,
        self::TYPE_EXTERNAL,
        self::TYPE_EXTERNAL_R,
        self::TYPE_VIDEO
    ];

    /**
     * Type titles
     *
     * @var array
     */
    public static $type_titles = [
        self::TYPE_FOLDER => 'Folder on computer (local/network)',
        self::TYPE_EXTERNAL => 'External storage (HDD/Flash/SD)',
        self::TYPE_EXTERNAL_R => 'External storage read-only (CD/DVD)',
        self::TYPE_VIDEO => 'Video storage (DVD/BD/VHS)'
    ];

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
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Storage
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
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Storage
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
     * Set path
     *
     * @param string $path
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Storage
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
     * Add item
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Item $item
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Storage
     */
    public function addItem(\AnimeDb\Bundle\CatalogBundle\Entity\Item $item)
    {
        $this->items[] = $item->setStorage($this);
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

    /**
     * Set type
     *
     * @param string $type
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Storage
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get supported types
     *
     * @return array
     */
    public static function getTypes()
    {
        return self::$type_names;
    }

    /**
     * Get title for current type
     *
     * @return string
     */
    public function getTypeTitle()
    {
        return self::$type_titles[$this->type];
    }

    /**
     * Get types storage allow write
     *
     * @return array
     */
    public static function getTypesWritable()
    {
        return [self::TYPE_FOLDER, self::TYPE_EXTERNAL];
    }

    /**
     * Get types storage allow read
     *
     * @return array
     */
    public static function getTypesReadable()
    {
        return [self::TYPE_FOLDER, self::TYPE_EXTERNAL, self::TYPE_EXTERNAL_R];
    }

    /**
     * Is path required to fill for current type of storage
     *
     * @return boolean
     */
    public function isPathRequired()
    {
        return in_array($this->getType(), self::getTypesWritable());
    }

    /**
     * Is valid path for current type
     *
     * @param \Symfony\Component\Validator\ExecutionContextInterface $context
     */
    public function isPathValid(ExecutionContextInterface $context)
    {
        if ($this->isPathRequired() && !$this->getPath()) {
            $context->addViolationAt('path', 'Path is required to fill for current type of storage');
        }
    }

    /**
     * Set date of last modified
     *
     * @param \DateTime $modified
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Storage
     */
    public function setModified(\DateTime $modified)
    {
        $this->modified = $modified;
        return $this;
    }

    /**
     * Get date of last modified
     *
     * @return \DateTime
     */
    public function getModified()
    {
        return $this->modified;
    }
}