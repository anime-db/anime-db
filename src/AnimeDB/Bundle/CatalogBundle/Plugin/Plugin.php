<?php
/**
 * AnimeDB package
 *
 * @package   AnimeDB
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDB\Bundle\CatalogBundle\Plugin;

/**
 * Plugin interface
 * 
 * @package AnimeDB\Bundle\CatalogBundle\Plugin
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
interface Plugin
{
    /**
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle();
}