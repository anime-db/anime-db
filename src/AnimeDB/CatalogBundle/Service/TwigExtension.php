<?php
/**
 * AnimeDB package
 *
 * @package   AnimeDB
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDB\CatalogBundle\Service;

use Symfony\Bundle\FrameworkBundle\Routing\Router;

/**
 * Twig extension
 *
 * @package AnimeDB\CatalogBundle\Service
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

    public function getFilters()
    {
        return array(
            'favicon' => new \Twig_Filter_Method($this, 'favicon'),
        );
    }

    public function favicon($url)
    {
        return $url ? $this->router->generate('media_favicon', ['host' => parse_url($url, PHP_URL_HOST)]) : false;
    }

    public function getName()
    {
        return 'animedb_extension';
    }
}