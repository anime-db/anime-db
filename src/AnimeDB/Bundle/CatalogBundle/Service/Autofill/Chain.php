<?php
/**
 * AnimeDB package
 *
 * @package   AnimeDB
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDB\Bundle\CatalogBundle\Service\Autofill;

use AnimeDB\Bundle\CatalogBundle\Service\Autofill\Filler;

/**
 * Chain
 * 
 * @package AnimeDB\Bundle\CatalogBundle\Service\Autofill
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Chain
{
    /**
     * List fillers
     *
     * @var array
     */
    private $fillers = array();

    /**
     * List filler titles
     *
     * @var array
     */
    private $filler_titles = array();

    /**
     * Add autofill filler
     *
     * @param \AnimeDB\Bundle\CatalogBundle\Service\Autofill\Filler $filler
     * @param string $alias
     */
    public function addFiller(Filler $filler, $alias) {
        $this->fillers[$alias] = $filler;
        $this->filler_titles[$alias] = $filler->getTitle();
    }

    /**
     * Get filler
     *
     * @param string $alias
     *
     * @return \AnimeDB\Bundle\CatalogBundle\Service\Autofill\Filler|null
     */
    public function getFiller($alias) {
        if (array_key_exists($alias, $this->fillers)) {
            return $this->fillers[$alias];
        }
        return null;
    }

    /**
     * Get filler by source
     *
     * @param string $source
     *
     * @return \AnimeDB\Bundle\CatalogBundle\Service\Autofill\Filler|null
     */
    public function getFillerBySource($source) {
        foreach ($this->fillers as $filler) {
            if ($filler->isSupportSource($source)) {
                return $filler;
            }
        }
        return null;
    }

    /**
     * Get filler names
     *
     * @return array
     */
    public function getFillerNames() {
        return array_keys($this->fillers);
    }

    /**
     * Get filler titles
     *
     * @return array
     */
    public function getFillerTitles() {
        return $this->filler_titles;
    }
}