<?php
/**
 * AnimeDB package
 *
 * @package   AnimeDB
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDB\CatalogBundle\Service\Pagination\Node;

use AnimeDB\CatalogBundle\Service\Pagination;
use AnimeDB\CatalogBundle\Service\Pagination\Node;

/**
 * Node for next page
 *
 * @package AnimeDB\CatalogBundle\Service\Pagination\Node
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Next extends Node
{
    /**
     * Name
     *
     * @var string
     */
    protected $name = '→';

    /**
     * Node title
     *
     * @var string
     */
    protected $title = 'Go to the next page';

    /**
     * Page number
     *
     * @var string
     */
    protected $type = Pagination::TYPE_NEXT;
}