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
 * Node for current page
 *
 * @package AnimeDB\CatalogBundle\Service\Pagination\Node
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Current extends Node
{
    /**
     * Node title
     *
     * @var string
     */
    protected $title = 'Current page';

    /**
     * Is current page
     *
     * @var boolean
     */
    protected $is_current = true;

    /**
     * Page number
     *
     * @var string
     */
    protected $type = Pagination::TYPE_CURENT;
}