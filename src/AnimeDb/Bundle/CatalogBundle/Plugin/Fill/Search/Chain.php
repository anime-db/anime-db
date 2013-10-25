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

use AnimeDb\Bundle\CatalogBundle\Plugin\Chain as ChainPlugin;

/**
 * Chain search plugins
 * 
 * @package AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Chain extends ChainPlugin
{
    /**
     * Name of default search plugin
     *
     * @var string
     */
    protected $default_search = '';

    /**
     * Construct
     *
     * @param string $default_search
     */
    public function __construct($default_search)
    {
        $this->default_search = $default_search;
    }

    /**
     * Get default search plugin
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Plugin\Plugin|null
     */
    public function getDafeultPlugin()
    {
        return $this->getPlugin($this->default_search);
    }
}