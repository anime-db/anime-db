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
 * Collection assets
 *
 * @package AnimeDb\Bundle\CatalogBundle\Service
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Assets
{
    /**
     * List links on JavaScripts files
     *
     * @var array
     */
    protected $javascripts = [];

    /**
     * List links on stylesheet files
     *
     * @var array
     */
    protected $stylesheet = [];

    /**
     * Get links on JavaScripts files
     *
     * @return array
     */
    public function getJavaScriptsPaths()
    {
        return $this->javascripts;
    }

    /**
     * Add links on JavaScripts file
     *
     * @param string $path
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Service\Assets
     */
    public function addJavaScriptsPath($path)
    {
        $this->javascripts[] = $path;
        return $this;
    }

    /**
     * Get links on stylesheet files
     *
     * @return array
     */
    public function getStylesheetPaths()
    {
        return $this->stylesheet;
    }

    /**
     * Add links on stylesheet file
     *
     * @param string $path
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Service\Assets
     */
    public function addStylesheetPath($path)
    {
        $this->stylesheet[] = $path;
        return $this;
    }
}