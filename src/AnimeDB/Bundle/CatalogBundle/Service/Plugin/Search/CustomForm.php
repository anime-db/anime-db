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

use AnimeDB\Bundle\CatalogBundle\Service\Plugin\Search\SearchInterface;

/**
 * Plugin has custom form for search
 * 
 * @package AnimeDB\Bundle\CatalogBundle\Service\Plugin\Search
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
interface CustomForm extends SearchInterface
{
    /**
     * Get form
     *
     * @return \Symfony\Component\Form\AbstractType
     */
    public function getForm();

    /**
     * Search source by form data
     *
     * Use $url_bulder for build link to fill item from source or build their own links
     *
     * Return structure
     * <code>
     * [
     *     \AnimeDB\Bundle\CatalogBundle\Service\Plugin\Search\Item
     * ]
     * </code>
     *
     * @param array $data
     * @param \Closure $url_bulder
     *
     * @return array
     */
    public function search(array $data, \Closure $url_bulder);
}