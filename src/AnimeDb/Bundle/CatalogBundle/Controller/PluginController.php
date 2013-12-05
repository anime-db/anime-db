<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Plugin
 *
 * @package AnimeDb\Bundle\CatalogBundle\Controller
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class PluginController extends Controller
{
    /**
     * Installed plugins
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function installedAction()
    {
        return $this->render('AnimeDbCatalogBundle:Plugin:installed.html.twig');
    }
}