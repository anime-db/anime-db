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
// use Symfony\Component\HttpFoundation\File\UploadedFile;
use AnimeDB\CatalogBundle\Entity\Field\Image as ImageField;
use AnimeDB\CatalogBundle\Form\Field\Image\Upload as UploadImage;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Form
 *
 * @package AnimeDB\CatalogBundle\Controller
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class FormController extends Controller
{
    /**
     * Allowed image extensions
     *
     * @var array
     */
    protected static $allowed_exts = ['bmp','gif','jpeg','jpg','jpe','png'];

    /**
     * Allowed image MIME-types
     *
     * @var array
     */
    protected static $allowed_mime = ['image/bmp','image/gif','image/jpeg','image/png'];

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
     * Upload image
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function uploadImageAction(Request $request) {
        $image = new ImageField();
        $form = $this->createForm(new UploadImage(), $image);
        $form->handleRequest($request);

        if (!$form->isValid()) {
            $errors = $form->getErrors();
            return new JsonResponse(['error' => $this->get('translator')->trans($errors[0]->getMessage())], 404);
        }

        // try upload file
        try {
            $image->upload($this->get('validator'));
            return new JsonResponse([
                'path'  => $image->getPath(),
                'image' => $image->getWebPath(),
            ]);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['error' => $this->get('translator')->trans($e->getMessage())], 404);
        }
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
        return $this->render('AnimeDBCatalogBundle:Form:plug/'.$template.'.html.twig', $request->query->all());
    }
}