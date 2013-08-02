<?php
/**
 * AnimeDB package
 *
 * @package   AnimeDB
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDB\Bundle\CatalogBundle\Service\Pagination\Node;

use AnimeDB\Bundle\CatalogBundle\Service\Pagination;
use AnimeDB\Bundle\CatalogBundle\Service\Pagination\Node;

/**
 * Node for previous page
 *
 * @package AnimeDB\Bundle\CatalogBundle\Service\Pagination\Node
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Previous extends Node
{
    /**
     * Name
     *
     * @var string
     */
    protected $name = '←';

    /**
     * Node title
     *
     * @var string
     */
    protected $title = 'Go to the previous page';

    /**
     * Page number
     *
     * @var string
     */
    protected $type = Pagination::TYPE_PREV;
}