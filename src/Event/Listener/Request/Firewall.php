<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AnimeDbBundle\Event\Listener\Request;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ServerBag;

/**
 * Firewall
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Event\Listener\Request
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Firewall
{
    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if ($event->getRequestType() !== HttpKernelInterface::MASTER_REQUEST) {
            return;
        }

        /* @var $server ServerBag */
        $server = $event->getRequest()->server;

        // Check that the access to the application by the local computer or local network
        if ($server->get('HTTP_CLIENT_IP') ||
            $server->get('HTTP_X_FORWARDED_FOR') ||
            !($addr = $server->get('REMOTE_ADDR')) ||
            (!$this->isLocalHost($addr) && !$this->isLocalNetwork($addr))
        ) {
            $response = new Response('You are not allowed to access this application.', Response::HTTP_FORBIDDEN);
            $event->setResponse($response->setPublic());
            $event->stopPropagation();
        }
    }

    /**
     * @param string $addr
     *
     * @return bool
     */
    protected function isLocalHost($addr)
    {
        return in_array($addr, ['127.0.0.1', 'fe80::1', '::1']);
    }

    /**
     * @param string $addr
     *
     * @return bool
     */
    protected function isLocalNetwork($addr)
    {
        // local network IPv6
        if (strpos($addr, ':') !== false) {
            return strpos($addr, 'fc00::') === 0;
        }

        if (($long = ip2long($addr)) === false) {
            return false;
        }

        return (
            ($long >= ip2long('10.0.0.0')    && $long <= ip2long('10.255.255.255')) ||
            ($long >= ip2long('172.16.0.0')  && $long <= ip2long('172.31.255.255')) ||
            ($long >= ip2long('192.168.0.0') && $long <= ip2long('192.168.255.255'))
        );
    }
}
