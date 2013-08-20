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
     * Return structure
     * <code>
     * [
     *     \AnimeDB\Bundle\CatalogBundle\Service\Plugin\Search\Item
     * ]
     * </code>
     *
     * @param array $data
     *
     * @return array
     */
    public function search(array $data);
}