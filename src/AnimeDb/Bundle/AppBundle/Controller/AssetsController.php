<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Assets
 *
 * @package AnimeDb\Bundle\AppBundle\Controller
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class AssetsController extends Controller
{

    /**
     * Show assets stylesheets and javascripts
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction()
    {
        return $this->render('AnimeDbAppBundle:Assets:show.html.twig', [
            'css' => $this->get('anime_db.assets')->getStylesheetPaths(),
            'js' => $this->get('anime_db.assets')->getJavaScriptsPaths()
        ]);
    }
}