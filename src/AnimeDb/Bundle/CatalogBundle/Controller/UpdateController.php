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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Process\PhpExecutableFinder;

/**
 * System update
 *
 * @package AnimeDb\Bundle\CatalogBundle\Controller
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class UpdateController extends Controller
{
    /**
     * Message identifies the end of the update
     *
     * @var string
     */
    const END_MESSAGE = '\r?\nUpdating the application has been completed\r?\n';

    /**
     * Update page
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        if ($request->getMethod() == 'POST') {
            // push event to execute update
            $host = $request->getHost().':'.$request->getPort();
            $fp = fsockopen($host, 80);
            $out = "POST ".$this->generateUrl('update_exec')." HTTP/1.1\r\n";
            $out .= "Host: ".$host."\r\n";
            $out .= "Connection: Close\r\n\r\n";
            fwrite($fp, $out);
            sleep(1);
            fclose($fp);
        }

        return $this->render('AnimeDbCatalogBundle:Update:index.html.twig', [
            'confirmed' => $request->getMethod() == 'POST',
            'log_file' => '/update.log',
            'end_message' => self::END_MESSAGE
        ]);
    }

    /**
     * Execute update
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function execAction()
    {
        ignore_user_abort(true);
        set_time_limit(0);

        $root = $this->container->getParameter('kernel.root_dir');
        $finder = new PhpExecutableFinder();
        $console = $finder->find().' '.$root.'/console';
        file_put_contents(($log = $root.'/../web/update.log'), '');

        chdir($root.'/../');
        exec($console.' animedb:update >'.$log.' &');

        return new Response();
    }
}