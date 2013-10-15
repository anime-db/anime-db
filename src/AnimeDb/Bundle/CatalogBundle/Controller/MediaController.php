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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

/**
 * Media
 *
 * @package AnimeDb\Bundle\CatalogBundle\Controller
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class MediaController extends Controller
{
    /**
     * MIME type list
     *
     * @var array
     */
    private static $mime = [
        'ico' => ['image/x-icon', 'image/vnd.microsoft.icon'],
        'png' => ['image/png']
    ];

    /**
     * Show favicon
     *
     * @param string $host
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function faviconAction($host)
    {
        $response = new Response('', 200, ['Content-Type' => self::$mime['ico'][0]]);

        $file = __DIR__.'/../../../../../web/media/favicon/'.$host.'.ico';
        if (file_exists($file)) {
            $response->setContent(file_get_contents($file));
        } else {
            $response = $this->downloadFavicon($response, $host, $file);
        }
        return $response;
    }

    /**
     * Download favicon
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @param string $host
     * @param string $target
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function downloadFavicon(Response $response, $host, $target)
    {
        try {
            $fs = new Filesystem();
            $fs->copy('http://'.$host.'/favicon.ico', $target);

            // site has a standard favicon
            if (($info = @getimagesize($target)) !== false) {
                return $response->setContent(file_get_contents($target));
            }
            $fs->remove($target);

            // search favicon in html
            $html = file_get_contents('http://'.$host.'/');
            preg_match_all('/<link[^>]+rel="(?:shortcut )?icon"[^>]+\/?>/is', $html, $icons);
            foreach ($icons[0] as $icon) {
                if (preg_match('/href="(?<url>.+?)"/', $icon, $mat)) {
                    $fs->copy($mat['url'], $target);
                    if (@getimagesize($target) !== false) {
                        return $response->setContent(file_get_contents($target));
                    }
                    $fs->remove($target);
                }
            }

            // no found favicon
            $response->setStatusCode(404);
        } catch (IOException $e) {
            $fs->remove($target);
            $response->setStatusCode(500);
        }

        return $response;
    }
}