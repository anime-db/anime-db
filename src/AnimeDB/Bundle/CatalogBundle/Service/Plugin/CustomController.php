<?php
/**
 * AnimeDB package
 *
 * @package   AnimeDB
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDB\Bundle\CatalogBundle\Service\Plugin;

/**
 * Custom controller interface
 * 
 * @package AnimeDB\Bundle\CatalogBundle\Service\Plugin
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
interface CustomController
{
    /**
     * Возвращает роут для контроллера
     *
     * @return string
     */
    public function getRoute();
}