<?php
/**
 * AnimeDB package
 *
 * @package   AnimeDB
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDB\CatalogBundle\Twig;

/**
 * Twig extension
 *
 * @package AnimeDB\CatalogBundle\Twig
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class AnimeDBExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            'favicon' => new \Twig_Filter_Method($this, 'favicon'),
        );
    }

    public function favicon($url)
    {
        if ($url) {
            $url = parse_url($url, PHP_URL_SCHEME).'://'.parse_url($url, PHP_URL_HOST).'/favicon.ico';
        }
        return $url;
    }

    public function getName()
    {
        return 'animedb_extension';
    }
}