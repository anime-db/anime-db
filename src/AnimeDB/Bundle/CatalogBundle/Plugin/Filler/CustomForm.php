<?php
/**
 * AnimeDB package
 *
 * @package   AnimeDB
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDB\Bundle\CatalogBundle\Plugin\Filler;

use AnimeDB\Bundle\CatalogBundle\Plugin\Filler\FillerInterface;

/**
 * Plugin has custom form for search
 * 
 * @package AnimeDB\Bundle\CatalogBundle\Plugin\Filler
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
interface CustomForm extends FillerInterface
{
    /**
     * Get form
     *
     * @return \Symfony\Component\Form\AbstractType
     */
    public function getForm();

    /**
     * Fill item from source data
     *
     * @param array $data
     *
     * @return \AnimeDB\Bundle\CatalogBundle\Entity\Item|null
     */
    public function fill(array $data);
}