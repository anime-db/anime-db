<?php
/**
 * AnimeDB package
 *
 * @package   AnimeDB
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDB\Bundle\CatalogBundle\Plugin\Import;

use AnimeDB\Bundle\CatalogBundle\Plugin\PluginInterface;

/**
 * Plugin import interface
 * 
 * @package AnimeDB\Bundle\CatalogBundle\Plugin\Import
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
interface Import extends PluginInterface
{
    /**
     * Get form
     *
     * @return \Symfony\Component\Form\AbstractType
     */
    public function getForm();

    /**
     * Import items from source data
     *
     * @param array $data
     *
     * @return array [ \AnimeDB\Bundle\CatalogBundle\Entity\Item ]
     */
    public function import(array $data);
}