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

use Symfony\Component\EventDispatcher\EventDispatcher;
use AnimeDb\Bundle\CatalogBundle\Event\Widget\StoreEvents;
use AnimeDb\Bundle\CatalogBundle\Event\Widget\Get;

/**
 * Widgets container
 *
 * @package AnimeDb\Bundle\CatalogBundle\Service
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class WidgetsContainer
{
    /**
     * Widgets
     *
     * @var array
     */
    private $widgets = [];

    /**
     * Dispatcher
     *
     * @var \Symfony\Component\EventDispatcher\EventDispatcher
     */
    private $dispatcher;

    /**
     * Construct
     *
     * @param \Symfony\Component\EventDispatcher\EventDispatcher $dispatcher
     */
    public function __construct(EventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Get list of widgets for place
     *
     * @param string $place
     *
     * @return array
     */
    public function getWidgetsForPlace($place)
    {
        // send the event to those who did not add widgets could do it
        $this->dispatcher->dispatch(StoreEvents::GET, new Get($this, $place));
        return isset($this->widgets[$place]) ? $this->widgets[$place] : [];
    }

    /**
     * Regist widget
     *
     * Controller example:
     *   AcmeDemoBundle:Welcome:index
     *   AcmeArticleBundle:Article:show
     *
     * @param string $place
     * @param string $controller
     *
     * @return boolean
     */
    public function registr($place, $controller)
    {
        if (preg_match('/^[a-z0-9]+:[a-z0-9]+:[_a-z0-9]+$/i', $controller)) {
            if (!in_array($controller, $this->widgets[$place])) {
                $this->widgets[$place][] = $controller;
            }
            return true;
        }
        return false;
    }

    /**
     * Unregist widget
     *
     * @param string $place
     * @param string $controller
     *
     * @return boolean
     */
    public function unregistr($place, $controller)
    {
        if (isset($this->widgets[$place]) &&
            ($key = array_search($controller, $this->widgets[$place])) !== false
        ) {
            unset($this->widgets[$place][$key]);
            return true;
        }
        return false;
    }
}