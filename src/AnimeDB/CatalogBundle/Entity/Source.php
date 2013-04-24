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
 * Source for item fill
 *
 * @ORM\Entity
 * @ORM\Table(name="source")
 * @IgnoreAnnotation("ORM")
 *
 * @package AnimeDB\CatalogBundle\Entity
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Source
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
     * URL
     *
     * @ORM\Column(type="string", length=256)
     * @Assert\NotBlank()
     * @Assert\Url()
     *
     * @var string
     */
    protected $url;

    /**
     * Items list
     *
     * @ORM\ManyToOne(targetEntity="Item", inversedBy="sources")
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
     * Set url
     *
     * @param string $url
     *
     * @return \AnimeDB\CatalogBundle\Entity\Source
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set item
     *
     * @param \AnimeDB\CatalogBundle\Entity\Item $item
     *
     * @return \AnimeDB\CatalogBundle\Entity\Source
     */
    public function setItem(Item $item = null)
    {
        if ($this->item != $item) {
            $this->item = $item;
            if ($item instanceof Item) {
                $this->item->addSource($this);
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
}