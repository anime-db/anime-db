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

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Gedmo\Translatable\TranslatableListener;

/**
 * Request listener
 *
 * @package AnimeDB\Bundle\CatalogBundle\Service\Listener
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Request
{
    /**
     * Session name for the locale
     *
     * @var string
     */
    const SESSION_LOCALE = '_locale';

    /**
     * Translatable listener
     *
     * @var \Gedmo\Translatable\TranslatableListener
     */
    protected $translatable;

    /**
     * Construct
     *
     * @param \Gedmo\Translatable\TranslatableListener $translatable
     */
    public function __construct(TranslatableListener $translatable)
    {
        $this->translatable = $translatable;
    }

    /**
     * Kernel request handler
     *
     * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $e
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        /* @var $request \Symfony\Component\HttpFoundation\Request */
        $request = $event->getRequest();

        // set default locale from request
        if ($locale = $request->getPreferredLanguage()) {
            $request->setDefaultLocale($locale);
        }

        setlocale(LC_ALL, $request->getLocale());

        // set locale from request attribute
        /* if ($locale = $request->attributes->get(self::SESSION_LOCALE)) {
            $request->setLocale($locale);
        } */

        // set locale from session
        /* if ($request->hasPreviousSession()) {
            if ($locale = $request->getSession()->get(self::SESSION_LOCALE)) {
                $request->setLocale($locale);
            } else {
                $request->getSession()->set(self::SESSION_LOCALE, $request->getLocale());
            }
        } */

        // set translatable language from locale
        $language = $request->getLocale();
        if (false !== ($position = strpos($language, '_'))) {
            $language = substr($language, 0, $position);
        }
        $this->translatable->setTranslatableLocale($language);
    }
}