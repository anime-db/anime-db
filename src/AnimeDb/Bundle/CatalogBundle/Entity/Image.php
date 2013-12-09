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
use AnimeDb\Bundle\CatalogBundle\Entity\Item;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Item images
 *
 * @ORM\Entity
 * @ORM\Table(name="image")
 * @ORM\HasLifecycleCallbacks
 * @IgnoreAnnotation("ORM")
 *
 * @package AnimeDb\Bundle\CatalogBundle\Entity
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Image
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
     * Source
     *
     * @ORM\Column(type="string", length=256)
     * @Assert\NotBlank()
     *
     * @var string
     */
    protected $source;

    /**
     * Items list
     *
     * @ORM\ManyToOne(targetEntity="Item", inversedBy="images", cascade={"persist"})
     * @ORM\JoinColumn(name="item", referencedColumnName="id")
     *
     * @var \AnimeDb\Bundle\CatalogBundle\Entity\Item
     */
    protected $item;

    /**
     * Old source list
     *
     * @var array
     */
    protected $old_sources = [];

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
     * Set source
     *
     * @param string $source
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Image
     */
    public function setSource($source)
    {
        if ($this->source) {
            $this->old_sources[] = $this->source;
        }
        $this->source = $source;
        return $this;
    }

    /**
     * Get source
     *
     * @return string 
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set item
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Item $item
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Image
     */
    public function setItem(Item $item = null)
    {
        if ($this->item !== $item) {
            $this->item = $item;
            if ($item instanceof Item) {
                $this->item->addImage($this);
            }
        }
        return $this;
    }

    /**
     * Get item
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Item
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * Remove source file
     *
     * @ORM\PostRemove
     */
    public function doRemoveSource()
    {
        if ($this->source && file_exists($this->getAbsolutePath())) {
            unlink($this->getAbsolutePath());
        }
    }

    /**
     * Remove old source files
     *
     * @ORM\PostRemove
     * @ORM\PostUpdate
     */
    public function doRemoveOldSources()
    {
        while ($cover = array_shift($this->old_sources)) {
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
        return $this->source !== null ? $this->getUploadRootDir().'/'.$this->source : null;
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

    /**
     * Get source web path
     *
     * @return string
     */
    public function getSourceWebPath()
    {
        return $this->source ? '/'.$this->getUploadDir().'/'.$this->source : null;
    }

    /**
     * Rename image if in temp folder
     *
     * @ORM\PrePersist
     */
    public function doRenameImageFile()
    {
        if ($this->source && strpos($this->source, 'tmp') !== false) {
            $filename = pathinfo($this->source, PATHINFO_BASENAME);
            $file = new File($this->getAbsolutePath());
            $this->source = date('Y/m/d/His/', $this->item->getDateAdd()->getTimestamp()).$filename;
            $file->move(pathinfo($this->getAbsolutePath(), PATHINFO_DIRNAME), $filename);
        }
    }
}