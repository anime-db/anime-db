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
use Guzzle\Http\Client;

/**
 * Plugin
 *
 * @package AnimeDb\Bundle\CatalogBundle\Controller
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class PluginController extends Controller
{
    /**
     * API server host
     *
     * @var string
     */
    const API_HOST = 'http://anime-db.org/';

    /**
     * Installed plugins
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function installedAction()
    {
        /* @var $repository \Doctrine\ORM\EntityRepository */
        $repository = $this->getDoctrine()->getRepository('AnimeDbAppBundle:Plugin');
        return $this->render('AnimeDbCatalogBundle:Plugin:installed.html.twig', [
            'plugins' => $repository->findAll()
        ]);
    }

    /**
     * Store of plugins
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function storeAction()
    {
        $client = new Client(self::API_HOST);
        /* @var $response \Guzzle\Http\Message\Response */
        $response = $client->get('api/plugin/')->send();

        if ($response->isSuccessful()) {
            $data = json_decode($response->getBody(true), true);
            $plugins = [];
            foreach ($data['plugins'] as $plugin) {
                $plugins[$plugin['name']] = $plugin;
                $plugins[$plugin['name']]['installed'] = false;
            }

            /* @var $repository \Doctrine\ORM\EntityRepository */
            $repository = $this->getDoctrine()->getRepository('AnimeDbAppBundle:Plugin');
            /* @var $plugin \AnimeDb\Bundle\AppBundle\Entity\Plugin */
            foreach ($repository->findAll() as $plugin) {
                $plugins[$plugin->getName()]['installed'] = true;
            }
        }

        return $this->render('AnimeDbCatalogBundle:Plugin:store.html.twig', [
            'plugins' => $plugins
        ]);
    }
}