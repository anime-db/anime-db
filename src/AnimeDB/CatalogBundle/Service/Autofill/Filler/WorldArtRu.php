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

use AnimeDB\CatalogBundle\Service\Autofill\Filler\Filler;

/**
 * Autofill from site world-art.ru
 * 
 * @link http://world-art.ru/
 * @package AnimeDB\CatalogBundle\Service\Autofill\Filler
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class WorldArtRu implements Filler
{
    /**
     * Title
     *
     * @var string
     */
    const NAME = 'World-Art.ru';

    /**
     * Path for search
     *
     * @var string
     */
    const SEARH_PATH = 'http://www.world-art.ru/search.php?public_search=#NAME#&global_sector=animation';

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle() {
        return self::NAME;
    }

    /**
     * Search source by name
     *
     * @param string $name
     *
     * @return array
     */
    public function search($name)
    {
        $path = str_replace('#NAME#', urlencode($name), self::SEARH_PATH);
        p($path);

        return array();
    }

    /**
     * Fill item from source
     *
     * @param string $source
     *
     * @return \AnimeDB\CatalogBundle\Entity\Item|null
     */
    public function fill($source)
    {
        // TODO requires the implementation of
        return null;
    }
}