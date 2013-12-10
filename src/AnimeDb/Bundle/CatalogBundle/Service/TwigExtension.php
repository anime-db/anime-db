<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Service;

/**
 * Twig extension
 *
 * @package AnimeDb\Bundle\CatalogBundle\Service
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class TwigExtension extends \Twig_Extension
{
    /**
     * (non-PHPdoc)
     * @see Twig_Extension::getFilters()
     */
    public function getFilters()
    {
        return [
            'dummy' => new \Twig_Filter_Method($this, 'dummy')
        ];
    }

    /**
     * Dummy for images
     *
     * @param string $path
     * @param string $filter
     *
     * @return boolean
     */
    public function dummy($path, $filter)
    {
        return $path ?: '/bundles/animedbcatalog/images/dummy/'.$filter.'.jpg';
    }

    /**
     * (non-PHPdoc)
     * @see Twig_ExtensionInterface::getName()
     */
    public function getName()
    {
        return 'anime_db_catalog_extension';
    }
}