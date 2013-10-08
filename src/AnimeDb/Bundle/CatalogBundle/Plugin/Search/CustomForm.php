<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Plugin\Search;

use AnimeDb\Bundle\CatalogBundle\Plugin\Search\Search;

/**
 * Plugin has custom form for search
 * 
 * @package AnimeDb\Bundle\CatalogBundle\Plugin\Search
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
abstract class CustomForm extends Search
{
    /**
     * Get form
     *
     * @return \Symfony\Component\Form\AbstractType
     */
    abstract public function getForm();

    /**
     * Search source by form data
     *
     * Use $url_bulder for build link to fill item from source or build their own links
     *
     * Return structure
     * <code>
     * [
     *     \AnimeDb\Bundle\CatalogBundle\Service\Plugin\Search\Item
     * ]
     * </code>
     *
     * @param array $data
     * @param \Closure $url_bulder
     *
     * @return array
     */
    abstract public function search($data, \Closure $url_bulder);
}