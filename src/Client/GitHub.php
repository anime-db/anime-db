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

use AnimeDb\Bundle\AnimeDbBundle\Composer\Composer;
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
        $last_version = '';
        $last_tag = [];
        foreach ($this->getTags($repository) as $tag) {
            if (($version = Composer::getVersionCompatible($tag['name'])) &&
                (!$last_version || version_compare($version, $last_version) != -1)
            ) {
                $last_version = $version;
                $last_tag = $tag;
            }
        }
        return $last_tag ?: false;
    }
}
