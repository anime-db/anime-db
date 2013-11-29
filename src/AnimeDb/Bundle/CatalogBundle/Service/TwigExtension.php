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
use AnimeDb\Bundle\CatalogBundle\Service\WidgetsContainer;

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
     * @var \AnimeDb\Bundle\CatalogBundle\Service\WidgetsContainer
     */
    private $widgets;

    /**
     * Hinclude loader template
     *
     * @var string
     */
    private $hinclude;

    /**
     * Construct
     *
     * @param \Symfony\Bundle\FrameworkBundle\Routing\Router $router
     * @param \Symfony\Component\HttpKernel\Fragment\FragmentHandler $handler
     * @param \AnimeDb\Bundle\CatalogBundle\Service\WidgetsContainer $widgets
     * @param string $hinclude
     */
    public function __construct(
        Router $router,
        FragmentHandler $handler,
        WidgetsContainer $widgets,
        $hinclude
    ) {
        $this->router = $router;
        $this->handler = $handler;
        $this->widgets = $widgets;
        $this->hinclude = $hinclude;
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
     * (non-PHPdoc)
     * @see Twig_Extension::getFunctions()
     */
    public function getFunctions()
    {
        return [
            'widgets' => new \Twig_Function_Method($this, 'widgets', ['is_safe' => ['html']])
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
     * @param array|null $options
     *
     * @return string
     */
    public function widgets($place, $attributes = [], $options = [])
    {
        $options = array_merge([
            'default' => $this->hinclude
        ], $options);

        $result = '';
        foreach ($this->widgets->getWidgetsForPlace($place) as $controller) {
            $result .= $this->handler->render(
                new ControllerReference($controller, $attributes, []),
                'hinclude',
                $options
            );
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