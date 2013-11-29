<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AppBundle\Event\Widget;

use Symfony\Component\EventDispatcher\Event;
use AnimeDb\Bundle\AppBundle\Service\WidgetsContainer;

/**
 * Event thrown when a widgets container get a list of widgets for place
 *
 * @package AnimeDb\Bundle\AppBundle\Event\Widget
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Get extends Event
{
    /**
     * Widgets container
     *
     * @var \AnimeDb\Bundle\AppBundle\Service\WidgetsContainer
     */
    protected $container;

    /**
     * Place for widgets
     *
     * @var string
     */
    protected $place;

    /**
     * Construct
     *
     * @param \AnimeDb\Bundle\AppBundle\Service\WidgetsContainer $container
     * @param string $place
     */
    public function __construct(WidgetsContainer $container, $place)
    {
        $this->container = $container;
        $this->place = $place;
    }

    /**
     * Get widgets container
     *
     * @return \AnimeDb\Bundle\AppBundle\Service\WidgetsContainer
     */
    public function getWidgetsContainer()
    {
        return $this->container;
    }

    /**
     * Get place for widgets
     *
     * @return string
     */
    public function getPlace()
    {
        return $this->place;
    }

    /**
     * Regist widget
     *
     * Controller example:
     *   AcmeDemoBundle:Welcome:index
     *   AcmeArticleBundle:Article:show
     *
     * @param string $controller
     *
     * @return boolean
     */
    public function registr($controller)
    {
        return $this->container->registr($this->place, $controller);
    }

    /**
     * Unregist widget
     *
     * @param string $controller
     *
     * @return boolean
     */
    public function unregistr($controller)
    {
        return $this->container->unregistr($this->place, $controller);
    }
}