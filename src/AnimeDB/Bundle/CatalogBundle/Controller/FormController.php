<?php
/**
 * AnimeDB package
 *
 * @package   AnimeDB
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDB\Bundle\CatalogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use AnimeDB\Bundle\CatalogBundle\Entity\Field\Image as ImageField;
use AnimeDB\Bundle\CatalogBundle\Form\Field\Image\Upload as UploadImage;
use AnimeDB\Bundle\CatalogBundle\Form\Field\LocalPath\Choice as ChoiceLocalPath;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Form
 *
 * @package AnimeDB\Bundle\CatalogBundle\Controller
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class FormController extends Controller
{
    /**
     * Form field local path
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function localPathAction(Request $request)
    {
        $form = $this->createForm(new ChoiceLocalPath(), ['path' => $request->get('path')]);

        return $this->render('AnimeDBCatalogBundle:Form:local_path.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Return list folders for path
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function localPathFoldersAction(Request $request)
    {
        $form = $this->createForm(new ChoiceLocalPath());
        $form->handleRequest($request);
        $path = $form->get('path')->getData();

        if (!$path || !is_dir($path) || !is_readable($path)) {
            throw new NotFoundHttpException('Cen\'t read directory: '.$path);
        }

        // add slash if need
        $path .= $path[strlen($path)-1] != DIRECTORY_SEPARATOR ? DIRECTORY_SEPARATOR : '';

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
                if ($entry == '..' || $entry[0] != '.') {
                    $folders[$entry] = [
                        'name' => $entry,
                        'path' => ($realpath != '/' ? $realpath.DIRECTORY_SEPARATOR : '/')
                    ];
                }
            }
        }
        $d->close();
        ksort($folders);

        return new JsonResponse(['folders' => array_values($folders)]);
    }

    /**
     * Form field image
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function imageAction(Request $request) {
        return $this->render('AnimeDBCatalogBundle:Form:image.html.twig', [
            'form' => $this->createForm(new UploadImage())->createView(),
            'change' => (bool)$request->get('change', false)
        ]);
    }

    /**
     * Upload image
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function imageUploadAction(Request $request) {
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