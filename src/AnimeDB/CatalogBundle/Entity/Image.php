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
use AnimeDB\CatalogBundle\Entity\Item;

/**
 * Item images
 *
 * @ORM\Entity
 * @ORM\Table(name="image")
 * @ORM\HasLifecycleCallbacks()
 * @IgnoreAnnotation("ORM")
 *
 * @package AnimeDB\CatalogBundle\Entity
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
     * @var \AnimeDB\CatalogBundle\Entity\Item
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
     * @return \AnimeDB\CatalogBundle\Entity\Image
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
     * @param \AnimeDB\CatalogBundle\Entity\Item $item
     *
     * @return \AnimeDB\CatalogBundle\Entity\Image
     */
    public function setItem(Item $item = null)
    {
        if ($this->item != $item) {
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
     * @return \AnimeDB\CatalogBundle\Entity\Item
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * Remove source file
     *
     * @ORM\postRemove
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
     * @ORM\postRemove
     * @ORM\postUpdate
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
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
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