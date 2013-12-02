<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AppBundle\Service\Pagination\Node;

use AnimeDb\Bundle\AppBundle\Service\Pagination;
use AnimeDb\Bundle\AppBundle\Service\Pagination\Node;

/**
 * Node for last page
 *
 * @package AnimeDb\Bundle\AppBundle\Service\Pagination\Node
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Last extends Node
{
    /**
     * Name
     *
     * @var string
     */
    protected $name = '››';

    /**
     * Node title
     *
     * @var string
     */
    protected $title = 'Go to the last page';

    /**
     * Page number
     *
     * @var string
     */
    protected $type = Pagination::TYPE_LAST;
}