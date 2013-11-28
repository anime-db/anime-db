<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Event\Listener;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Gedmo\Translatable\TranslatableListener;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Symfony\Component\Validator\Validator;
use Symfony\Component\Validator\Constraints\Locale;

/**
 * Request listener
 *
 * @package AnimeDb\Bundle\CatalogBundle\Event\Listener
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
     * Validator
     *
     * @var \Symfony\Component\Validator\Validator
     */
    private $validator;

    /**
     * Construct
     *
     * @param \Gedmo\Translatable\TranslatableListener $translatable
     * @param \Symfony\Component\Validator\Validator $validator
     */
    public function __construct(TranslatableListener $translatable, Validator $validator)
    {
        $this->translatable = $translatable;
        $this->validator = $validator;
    }

    /**
     * Kernel request handler
     *
     * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        /* @var $request \Symfony\Component\HttpFoundation\Request */
        $request = $event->getRequest();

        // set default locale from request
        if ($request_locale = $request->getPreferredLanguage()) {
            $request->setDefaultLocale($request_locale);
        }

        // reset locale
        $this->setLocale($request, $this->getLocale($request));
    }

    /**
     * Set current locale
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $locale
     */
    public function setLocale(HttpRequest $request, $locale)
    {
        // set locale from session
        if ($request->hasPreviousSession()) {
            $request->getSession()->set(self::SESSION_LOCALE, $locale);
        }
        $request->setLocale($locale);
        setlocale(LC_ALL, $locale);
        // set translatable
        $this->translatable->setTranslatableLocale($locale);
    }

    /**
     * Get current locale
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return string
     */
    public function getLocale(HttpRequest $request)
    {
        // set locale from session
        if ($request->hasPreviousSession()) {
            if ($locale = $request->getSession()->get(self::SESSION_LOCALE)) {
                return $locale;
            }
        }

        // get locale from language list
        $locale_constraint = new Locale();
        foreach ($request->getLanguages() as $language) {
            if (!count($this->validator->validateValue($language, $locale_constraint))) {
                return $language;
            }
        }

        // get default locale
        return $request->getLocale();
    }
}