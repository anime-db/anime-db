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
use AnimeDb\Bundle\AppBundle\Entity\Notice;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AnimeDb\Bundle\AppBundle\Service\Pagination;

/**
 * Notice
 *
 * @package AnimeDb\Bundle\AppBundle\Controller
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class NoticeController extends Controller
{
    /**
     * Show last notice
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction()
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $repository \AnimeDb\Bundle\AppBundle\Repository\Notice */
        $repository = $em->getRepository('AnimeDbAppBundle:Notice');

        $notice = $repository->getFirstShow();

        // shown notice
        if (!is_null($notice)) {
            $notice->shown();
            $em->persist($notice);
            $em->flush();

            return new JsonResponse([
                'notice' => $notice->getId(),
                'close' => $this->generateUrl('notice_close', ['id' => $notice->getId()]),
                'content' => $this->renderView('AnimeDbAppBundle:Notice:show.html.twig', ['notice' => $notice])
            ]);
        }

        return new JsonResponse([]);
    }

    /**
     * Close notice
     *
     * @param \AnimeDb\Bundle\AppBundle\Entity\Notice $notice
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
}