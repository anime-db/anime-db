<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Service\Pagination\Node;

use AnimeDb\Bundle\CatalogBundle\Service\Pagination;
use AnimeDb\Bundle\CatalogBundle\Service\Pagination\Node;

/**
 * Node for previous page
 *
 * @package AnimeDb\Bundle\CatalogBundle\Service\Pagination\Node
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