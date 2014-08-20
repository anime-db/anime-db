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
        $last_version = '';
        $last_tag = [];
        foreach ($this->getTags($repository) as $tag) {
            if (($version = $this->getVersionCompatible($tag['name'])) &&
                (!$last_version || version_compare($version, $last_version) != -1)
            ) {
                $last_version = $version;
                $last_tag = $tag;
            }
        }
        return $last_tag ?: false;
    }

    /**
     * Get version compatible
     *
     * 3.2.1-RC2 => 3.2.1.6.2
     *
     * @param string $version
     *
     * @return string|false
     */
    public function getVersionCompatible($version)
    {
        // {suffix:weight}
        $suffixes = [
            'dev' => 1, // composer suffix
            'patch' => 2,
            'alpha' => 3,
            'beta' => 4,
            'stable' => 5, // is not a real suffix. use it if suffix is not exists
            'rc' => 6
        ];

        $reg = '/^v?(?<version>\d+\.\d+\.\d+)(?:-(?<suffix>dev|patch|alpha|beta|rc)(?<suffix_version>\d+)?)?$/i';
        if (!preg_match($reg, $version, $match)) {
            return false;
        }

        // suffix version
        if (isset($match['suffix'])) {
            $suffix = $suffixes[strtolower($match['suffix'])].'.';
            $suffix .= isset($match['suffix_version']) ? (int)$match['suffix_version'] : 1;
        } else {
            $suffix = $suffixes['stable'].'.0';
        }

        return $match['version'].'.'.$suffix;
    }
}