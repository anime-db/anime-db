<?php
/**
 * AnimeDB package
 *
 * @package   AnimeDB
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDB\CatalogBundle\Service\Autofill\Filler;

/**
 * Autofill filler interface
 * 
 * @package AnimeDB\CatalogBundle\Service\Autofill\Filler
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
     *     {
     *         'name': string,
     *         'source': string,
     *         'description': string
     *     }
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
     * @return \AnimeDB\CatalogBundle\Entity\Item|null
     */
    public function fill($source);
}