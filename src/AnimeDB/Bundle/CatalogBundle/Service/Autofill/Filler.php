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

/**
 * Autofill filler interface
 * 
 * @package AnimeDB\Bundle\CatalogBundle\Service\Autofill
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
interface Filler
{
    /**
     * Get title
     *
     * @return string
     */
    public function getTitle();

    /**
     * Search source by name
     *
     * Return structure
     * <code>
     * [
     *     \AnimeDB\Bundle\CatalogBundle\Service\Autofill\Search\Item
     * ]
     * </code>
     *
     * @param string $name
     *
     * @return array
     */
    public function search($name);

    /**
     * Fill item from source
     *
     * @param string $source
     *
     * @return \AnimeDB\Bundle\CatalogBundle\Entity\Item|null
     */
    public function fill($source);

    /**
     * Filler is support this source
     *
     * @param string $source
     *
     * @return boolean
     */
    public function isSupportSource($source);
}