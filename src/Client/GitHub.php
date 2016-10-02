<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\AnimeDbBundle\Client;

use AnimeDb\Bundle\AnimeDbBundle\Composer\Composer;
use Guzzle\Http\Client;
use Guzzle\Http\Message\Response;

class GitHub
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @param string $api
     * @param Client $client
     */
    public function __construct($api, Client $client)
    {
        $this->client = $client->setBaseUrl($api);
    }

    /**
     * @param string $repository
     *
     * @return array
     */
    public function getTags($repository)
    {
        /* @var $response Response */
        $response = $this->client->get('repos/'.$repository.'/tags')->send();

        return json_decode($response->getBody(true), true);
    }

    /**
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
                (!$last_version || version_compare($version, $last_version, '>=')) &&
                version_compare($version, '1.0.0', '<') // v1.0.0 is BC
            ) {
                $last_version = $version;
                $last_tag = $tag;
            }
        }

        return $last_tag ?: false;
    }
}
