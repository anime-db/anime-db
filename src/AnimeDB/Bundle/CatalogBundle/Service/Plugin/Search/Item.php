<?php
/**
 * AnimeDB package
 *
 * @package   AnimeDB
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDB\Bundle\CatalogBundle\Service\Plugin\Search;

/**
 * Element search results
 * 
 * @package AnimeDB\Bundle\CatalogBundle\Service\Plugin\Search
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
     * Source
     *
     * @var string
     */
    protected $source = '';

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
     * @param string $source
     * @param string $image
     * @param string $description
     */
    public function __construct($name, $source, $image, $description)
    {
        $this->name = $name;
        $this->source = $source;
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
     * Get source
     *
     * @return string
     */
    public function getSource()
    {
        return $this->source;
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