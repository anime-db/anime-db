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
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var integer
     */
    protected $id;

    /**
     * Source
     *
     * @ORM\Column(type="string", length=256)
     * @Assert\NotBlank()
     * @Assert\Image()
     *
     * @var string
     */
    protected $source;

    /**
     * Items list
     *
     * @ORM\ManyToOne(targetEntity="Item", inversedBy="images")
     * @ORM\JoinColumn(name="id", referencedColumnName="id")
     *
     * @var \AnimeDB\CatalogBundle\Entity\Item
     */
    protected $item;

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

    public function getAbsolutePath()
    {
        return $this->cover !== null ? $this->getUploadRootDir().'/'.$this->cover : null;
    }

    public function getWebPath()
    {
        return $this->cover !== null ? $this->getUploadDir().'/'.$this->cover : null;
    }

    protected function getUploadRootDir()
    {
        return realpath(__DIR__.'/../../../../web/'.$this->getUploadDir());
    }

    protected function getUploadDir()
    {
        return 'media';
    }
}