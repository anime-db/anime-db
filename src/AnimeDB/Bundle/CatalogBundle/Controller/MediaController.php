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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

/**
 * Media
 *
 * @package AnimeDB\Bundle\CatalogBundle\Controller
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
        'ico' => ['image/x-icon', 'image/vnd.microsoft.icon']
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
        $status = 200;
        $content = '';
        $file = realpath(__DIR__.'/../../../../web').'/media/favicon/'.$host.'.ico';
        if (!file_exists($file)) {
            $fs = new Filesystem();
            // download favicon
            try {
                $fs->copy('http://'.$host.'/favicon.ico', $file);
                // site does not have a favicon
                if (($info = @getimagesize($file)) === false || !in_array($info['mime'], self::$mime['ico'])) {
                    $fs->remove($file);
                    $status = 404;
                } else {
                    $content = file_get_contents($file);
                }
            } catch (IOException $e) {
                $status = 500;
            }
        }
        return new Response($content, $status, ['Content-Type' => self::$mime['ico'][0]]);
    }
}