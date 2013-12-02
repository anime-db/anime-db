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
use AnimeDb\Bundle\AppBundle\Entity\Notice;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AnimeDb\Bundle\AppBundle\Service\Pagination;

/**
 * Notice
 *
 * @package AnimeDb\Bundle\CatalogBundle\Controller
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class NoticeController extends Controller
{
    /**
     * Number of notices per page
     *
     * @var integer
     */
    const NOTICE_PER_PAGE = 30;

    /**
     * Get one notice
     *
     * @param \AnimeDb\Bundle\AppBundle\Entity\Notice $notice
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getAction(Notice $notice)
    {
        return $this->render('AnimeDbCatalogBundle:Notice:get.html.twig', ['notice' => $notice]);
    }

    /**
     * Get notice list
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Request $request)
    {
        $current_page = $request->get('page', 1);
        $current_page = $current_page > 1 ? $current_page : 1;

        $em = $this->getDoctrine()->getManager();
        /* @var $repository \AnimeDb\Bundle\AppBundle\Repository\Notice */
        $repository = $em->getRepository('AnimeDbAppBundle:Notice');

        // get notices
        $notices = $repository->getList(self::NOTICE_PER_PAGE, ($current_page - 1) * self::NOTICE_PER_PAGE);

        // remove notices if need
        if ($request->isMethod('POST') && $notices) {
            if ((int)$request->request->get('check-all', 0)) { // remove all entitys
                foreach ($notices as $notice) {
                    $em->remove($notice);
                }
                $em->flush();
            } elseif ($ids = (array)$request->request->get('id', [])) { // remove selected entitys
                foreach ($ids as $id) {
                    foreach ($notices as $notice) {
                        if ($notice->getId() == $id) {
                            $em->remove($notice);
                            break;
                        }
                    }
                }
                $em->flush();
            }
            return $this->redirect($this->generateUrl('notice_list'));
        }

        // get count all items
        $count = $repository->count();

        $that = $this;
        $pagination = $this->get('anime_db.pagination')->createNavigation(
            ceil($count/self::NOTICE_PER_PAGE),
            $current_page,
            Pagination::DEFAULT_LIST_LENGTH,
            function ($page) use ($that) {
                return $that->generateUrl('notice_list', ['page' => $page]);
            },
            $this->generateUrl('notice_list')
        );

        return $this->render('AnimeDbCatalogBundle:Notice:list.html.twig', [
            'list' => $notices,
            'pagination' => $pagination
        ]);
    }
}