<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Plugin\Import;

use AnimeDb\Bundle\CatalogBundle\Plugin\Plugin;

/**
 * Plugin import interface
 * 
 * @package AnimeDb\Bundle\CatalogBundle\Plugin\Import
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
interface Import extends Plugin
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
     * @return array [ \AnimeDb\Bundle\CatalogBundle\Entity\Item ]
     */
    public function import(array $data);
}