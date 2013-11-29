<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search;

/**
 * Element search results
 * 
 * @package AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Item
{
    /**
     * Name
     *
     * @var string
     */
    protected $name = '';

    /**
     * Link to fill item from source
     *
     * @var string
     */
    protected $link = '';

    /**
     * Image
     *
     * @var string
     */
    protected $image = '';

    /**
     * Description
     *
     * @var string
     */
    protected $description = '';

    /**
     * Construct
     *
     * @param string $name
     * @param string $link
     * @param string $image
     * @param string $description
     */
    public function __construct($name, $link, $image, $description)
    {
        $this->name = $name;
        $this->link = $link;
        $this->image = $image;
        $this->description = $description;
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
     * Get link to fill item from source
     *
     * @return string
     */
    public function getLink()
    {
        return $this->link;
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
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}