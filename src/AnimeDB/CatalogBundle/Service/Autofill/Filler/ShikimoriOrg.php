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
 * Autofill from site shikimori.org
 * 
 * @link http://shikimori.org/
 * @package AnimeDB\CatalogBundle\Service\Autofill\Filler
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class ShikimoriOrg implements Filler
{
    /**
     * Title
     *
     * @var string
     */
    const NAME = 'Shikimori.org';

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle() {
        return self::NAME;
    }
}