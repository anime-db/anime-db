<?php
/**
 * AnimeDB package
 *
 * @package   AnimeDB
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDB\CatalogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Form
 *
 * @package AnimeDB\CatalogBundle\Controller
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class FormController extends Controller
{
    /**
     * Return list folders for directory
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function foldersAction(Request $request)
    {
        $path = realpath($request->get('path', __DIR__.'/../../../../'));
        if (!$path || !is_dir($path) || !is_readable($path)) {
            throw $this->createNotFoundException('Cen\'t read directory: '.$path);
        }
        // add slash if need
        $path .= $path[strlen($path)-1] != DIRECTORY_SEPARATOR ? DIRECTORY_SEPARATOR : '';
        $show_hidden = (int)$request->get('show_hidden', 0);

        // scan directory
        $d = dir($path);
        $folders = [];
        while (false !== ($entry = $d->read())) {
            if ($entry == '.') {
                continue;
            }
            $realpath = realpath($path.$entry.DIRECTORY_SEPARATOR);
            // if read path is root path then parent path is also equal to root
            if ($realpath && $realpath != $path && is_dir($realpath) && is_readable($realpath)) {
                if ($entry == '..' || $entry[0] != '.' || $show_hidden) {
                    $folders[$entry] = [
                        'name' => $entry,
                        'path' => $realpath.DIRECTORY_SEPARATOR
                    ];
                }
            }
        }
        $d->close();
        ksort($folders);

        return new JsonResponse(['folders' => array_values($folders)]);
    }

    /**
     * Rand and return template
     *
     * @param string $template
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function templateAction($template, Request $request)
    {
        return $this->renderView('AnimeDBCatalogBundle:Form:plug/'.$template.'.html.twig', $request->query->all());
    }
}