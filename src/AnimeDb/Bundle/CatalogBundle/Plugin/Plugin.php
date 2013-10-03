<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Plugin;

/**
 * Plugin interface
 * 
 * @package AnimeDb\Bundle\CatalogBundle\Plugin
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