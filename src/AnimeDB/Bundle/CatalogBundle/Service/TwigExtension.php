<?php
/**
 * AnimeDB package
 *
 * @package   AnimeDB
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDB\Bundle\CatalogBundle\Service;

use Symfony\Bundle\FrameworkBundle\Routing\Router;

/**
 * Twig extension
 *
 * @package AnimeDB\Bundle\CatalogBundle\Service
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class TwigExtension extends \Twig_Extension
{
    /**
     * Router
     *
     * @var \Symfony\Bundle\FrameworkBundle\Routing\Router
     */
    private $router;

    /**
     * Construct
     *
     * @param \Symfony\Bundle\FrameworkBundle\Routing\Router $router
     */
    public function __construct(Router $router) {
        $this->router = $router;
    }

    /**
     * (non-PHPdoc)
     * @see Twig_Extension::getFilters()
     */
    public function getFilters()
    {
        return [
            'favicon' => new \Twig_Filter_Method($this, 'favicon'),
            'dummy' => new \Twig_Filter_Method($this, 'dummy')
        ];
    }

    /**
     * Favicon
     *
     * @param string $url
     *
     * @return boolean
     */
    public function favicon($url)
    {
        return $url ? $this->router->generate('media_favicon', ['host' => parse_url($url, PHP_URL_HOST)]) : false;
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
        return $path ?: '/media/dummy/'.$filter.'.jpg';
    }

    /**
     * (non-PHPdoc)
     * @see Twig_ExtensionInterface::getName()
     */
    public function getName()
    {
        return 'animedb_extension';
    }
}