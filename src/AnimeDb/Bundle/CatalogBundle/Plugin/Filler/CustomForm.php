<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Plugin\Filler;

use AnimeDb\Bundle\CatalogBundle\Plugin\Filler\Filler;

/**
 * Plugin has custom form for search
 * 
 * @package AnimeDb\Bundle\CatalogBundle\Plugin\Filler
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
abstract class CustomForm extends Filler
{
    /**
     * Get form
     *
     * @return \Symfony\Component\Form\AbstractType
     */
    abstract public function getForm();
}