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
use Symfony\Component\HttpFoundation\Request;

/**
 * Static files
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Event\Listener\Request
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class StaticFiles
{
    /**
     * @var string
     */
    protected $root_dir;

    /**
     * @var string
     */
    protected $env;

    /**
     * @param string $root_dir
     * @param string $env
     */
    public function __construct($root_dir, $env)
    {
        $this->root_dir = $root_dir;
        $this->env = $env;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if ($event->getRequestType() !== HttpKernelInterface::MASTER_REQUEST) {
            return;
        }

        /* @var $request Request */
        $request = $event->getRequest();

        $file = $request->getScriptName() == '/app_dev.php' ? $request->getPathInfo() : $request->getScriptName();
        if (is_file($file = $this->root_dir.'/../web'.$file)) {
            $response = (new Response())->setPublic();

            // caching in prod env
            if ($this->env == 'prod') {
                $response
                    ->setEtag(md5_file($file))
                    ->setExpires((new \DateTime())->setTimestamp(time()+2592000)) // updates interval of 30 days
                    ->setLastModified((new \DateTime())->setTimestamp(filemtime($file)))
                    ->headers->addCacheControlDirective('must-revalidate', true);
                // response was not modified for this request
                if ($response->isNotModified($request)) {
                    $event->setResponse($response);
                    return;
                }
            }

            // set content type
            $mimes = [
                'css' => 'text/css',
                'js' => 'text/javascript'
            ];

            if (isset($mimes[($ext = pathinfo($file, PATHINFO_EXTENSION))])) {
                $response->headers->set('Content-Type', $mimes[$ext]);
            } else {
                $response->headers->set('Content-Type', mime_content_type($file));
            }

            $event->setResponse($response->setContent(file_get_contents($file)));
        }
    }
}
