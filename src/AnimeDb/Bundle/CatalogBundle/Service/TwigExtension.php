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

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpKernel\Fragment\FragmentHandler;
use Symfony\Component\HttpKernel\Controller\ControllerReference;
use AnimeDb\Bundle\CatalogBundle\Service\WidgetContainer;

/**
 * Twig extension
 *
 * @package AnimeDb\Bundle\CatalogBundle\Service
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
     * Handler
     *
     * @var \Symfony\Component\HttpKernel\Fragment\FragmentHandler
     */
    private $handler;

    /**
     * Widget container
     *
     * @var \AnimeDb\Bundle\CatalogBundle\Service\WidgetContainer
     */
    private $widgets;

    /**
     * Construct
     *
     * @param \Symfony\Bundle\FrameworkBundle\Routing\Router $router
     * @param \Symfony\Component\HttpKernel\Fragment\FragmentHandler $handler
     * @param \AnimeDb\Bundle\CatalogBundle\Service\WidgetContainer $widgets
     */
    public function __construct(Router $router, FragmentHandler $handler, $widgets) {
        $this->router = $router;
        $this->handler = $handler;
        $this->widgets = $widgets;
    }

    /**
     * (non-PHPdoc)
     * @see Twig_Extension::getFilters()
     */
    public function getFilters()
    {
        return [
            'favicon' => new \Twig_Filter_Method($this, 'favicon'),
            'dummy' => new \Twig_Filter_Method($this, 'dummy'),
            'widgets' => new \Twig_Function_Method($this, 'widgets')
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
     * Render widgets
     *
     * @param string $place
     * @param array|null $attributes
     *
     * @return string
     */
    public function widgets($place, $attributes = [])
    {
        $result = '';
        foreach ($this->widgets->getWidgetsForPlace($place) as $controller) {
            $result .= $this->handler->render(new ControllerReference($controller, $attributes, []), 'inline', []);
        }
        return $result;
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