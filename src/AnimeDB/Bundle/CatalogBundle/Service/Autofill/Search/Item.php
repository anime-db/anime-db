<?php
/**
 * AnimeDB package
 *
 * @package   AnimeDB
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDB\Bundle\CatalogBundle\Service\Autofill\Search;

/**
 * Element search results
 * 
 * @package AnimeDB\Bundle\CatalogBundle\Service\Autofill\Search
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
     * @param string $description
     */
    public function __construct($name, $source, $description)
    {
        $this->name = $name;
        $this->source = $source;
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
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}