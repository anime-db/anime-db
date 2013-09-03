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
use AnimeDB\Bundle\CatalogBundle\Entity\Notice;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
/**
 * Notice
 *
 * @package AnimeDB\Bundle\CatalogBundle\Controller
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
     * Show last notice
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction()
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AnimeDBCatalogBundle:Notice');

        $notice = $repository
            ->createQueryBuilder('n')
            ->andWhere('n.status != :closed')
            ->andWhere('(n.date_closed IS NULL OR n.date_closed >= :time)')
            ->setParameter('closed', Notice::STATUS_CLOSED)
            ->setParameter('time', date('Y-m-d H:i:s'))
            ->addOrderBy('n.date_created', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        // shown notice
        if (!is_null($notice)) {
            $notice->shown();
            $em->persist($notice);
            $em->flush();

            return new JsonResponse([
                'notice' => $notice->getId(),
                'close' => $this->generateUrl('notice_close', ['id' => $notice->getId()]),
                'content' => $this->renderView('AnimeDBCatalogBundle:Notice:show.html.twig', ['notice' => $notice])
            ]);
        }

        return new JsonResponse([]);
    }

    /**
     * Close notice
     *
     * @param \AnimeDB\Bundle\CatalogBundle\Entity\Notice $notice
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function closeAction(Notice $notice)
    {
        // mark as closed
        $notice->setStatus(Notice::STATUS_CLOSED);
        $em = $this->getDoctrine()->getManager();
        $em->persist($notice);
        $em->flush();

        return new JsonResponse([]);
    }

    /**
     * Get one notice
     *
     * @param \AnimeDB\Bundle\CatalogBundle\Entity\Notice $notice
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getAction(Notice $notice)
    {
        return $this->render('AnimeDBCatalogBundle:Notice:get.html.twig', ['notice' => $notice]);
    }

    /**
     * Get notice list
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getListAction(Request $request)
    {
        $current_page = $request->get('page', 1);
        $current_page = $current_page > 1 ? $current_page : 1;

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AnimeDBCatalogBundle:Notice');

        // get notices
        $notices = $repository
            ->createQueryBuilder('n')
            ->addOrderBy('n.date_created', 'DESC')
            ->setFirstResult(($current_page - 1) * self::NOTICE_PER_PAGE)
            ->setMaxResults(self::NOTICE_PER_PAGE)
            ->getQuery()
            ->getResult();

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
            return $this->redirect($this->generateUrl('notice_get_list'));
        }

        return $this->render('AnimeDBCatalogBundle:Notice:list.html.twig', ['list' => $notices]);
    }
}