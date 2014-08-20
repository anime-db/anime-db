<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AnimeDbBundle\Client;

use Guzzle\Http\Client;

/**
 * GitHub client
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Client
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class GitHub
{
    /**
     * Client
     *
     * @var \Guzzle\Http\Client
     */
    protected $client;

    /**
     * Construct
     *
     * @param string $api
     * @param \Guzzle\Http\Client $client
     */
    public function __construct($api, Client $client)
    {
        $this->client = $client->setBaseUrl($api);
    }

    /**
     * Get list of tags
     *
     * @param string $repository
     *
     * @return array
     */
    public function getTags($repository)
    {
        /* @var $response \Guzzle\Http\Message\Response */
        $response = $this->client->get('repos/'.$repository.'/tags')->send();
        return json_decode($response->getBody(true), true);
    }

    /**
     * Get last release
     *
     * @param string $repository
     *
     * @return array|false
     */
    public function getLastRelease($repository)
    {
        $last = [];
        // search tag with new version of application
        $reg = '/^v?(?<version>\d+\.\d+\.\d+)(?:-(?:dev|patch|alpha|beta|rc)(?<suffix>\d+))?$/i';
        foreach ($this->getTags($repository) as $tag) {
            if (preg_match($reg, $tag['name'], $mat)) {
                $version = $mat['version'].'.'.(isset($mat['suffix']) ? $mat['suffix'] : '0');
                if (!$last || version_compare($version, $last['version']) == 1) {
                    $last = array_merge(['version' => $version], $tag);
                }
            }
        }
        return $last ?: false;
    }
}