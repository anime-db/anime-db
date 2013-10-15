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

use Knp\Menu\ItemInterface;

/**
 * Plugin
 * 
 * @package AnimeDb\Bundle\CatalogBundle\Plugin
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
abstract class Plugin
{
    /**
     * Get name
     *
     * @return string
     */
    abstract public function getName();

    /**
     * Get title
     *
     * @return string
     */
    abstract public function getTitle();
}