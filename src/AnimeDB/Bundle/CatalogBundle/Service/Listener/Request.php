<?php
/**
 * AnimeDB package
 *
 * @package   AnimeDB
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDB\Bundle\CatalogBundle\Service\Listener;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Session\Session;
/**
 * Request listener
 *
 * @package AnimeDB\Bundle\CatalogBundle\Service\Listener
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Request
{
    /**
     * Container
     *
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * Default locale
     *
     * @var string
     */
    protected $default = 'en';

    /**
     * Construct
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     * @param string $default
     */
    public function __construct(ContainerInterface $container, $default = 'en')
    {
        $this->container = $container;
        $this->default = $default;
    }

    /**
     * Kernel request handler
     *
     * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $e
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $request = $event->getRequest();
        $locale = $request->getPreferredLanguage() ?: $this->default;
        $request->setLocale($locale);
    }
}