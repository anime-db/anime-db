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
     * Get list of widgets for place
     *
     * @param string $place
     *
     * @return array
     */
    public function getWidgetsForPlace($place)
    {
        return isset($this->widgets[$place]) ? $this->widgets[$place] : [];
    }

    /**
     * Regist widget
     *
     * Controller example:
     *   AcmeDemoBundle:Welcome:index
     *   AcmeArticleBundle:Article:show
     *
     * @param string $controller
     * @param string $place
     *
     * @return boolean
     */
    public function registr($place, $controller)
    {
        if (preg_match('/^[a-z0-9]+:[a-z0-9]+:[_a-z0-9]+$/i', $controller)) {
            $this->widgets[$place][] = $controller;
            return true;
        }
        return false;
    }

    /**
     * Unregist widget
     *
     * @param string $controller
     * @param string $place
     *
     * @return boolean
     */
    public function unregistr($place, $controller)
    {
        if (isset($this->widgets[$place])) {
            foreach ($this->widgets[$place] as $key => $widget) {
                if ($widget == $controller) {
                    unset($this->widgets[$place][$key]);
                    return true;
                }
            }
        }
        return false;
    }
}