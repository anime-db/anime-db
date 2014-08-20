<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AnimeDbBundle\Tests\Client;

use AnimeDb\Bundle\AnimeDbBundle\Client\GitHub;

/**
 * Test client GitHub
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Tests\Client
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class GitHubTest extends \PHPUnit_Framework_TestCase
{
    /**
     * API host
     *
     * @var string
     */
    protected $api_host = 'api://host';

    /**
     * Repository
     *
     * @var string
     */
    protected $repository = 'foo/bar';

    /**
     * Test get tags
     */
    public function testGetTags()
    {
        $expected = $this->getTags()[0][0];
        $this->assertEquals($expected, $this->getClient($expected)->getTags($this->repository));
    }

    /**
     * Get tags
     *
     * @return array
     */
    public function getTags()
    {
        return [
            [
                [
                    ['name' => '1.0'], // bad version
                    ['name' => '1.0.0'],
                    ['name' => '1.1.0-dev'],
                    ['name' => '1.1.0-patch'],
                    ['name' => '1.1.0-alpha'],
                    ['name' => '1.1.0-beta'],
                    ['name' => '1.1.0'],
                    ['name' => '1.1.0-RC'],
                ],
                ['name' => '1.1.0-RC'],
            ],
            [
                [
                    ['name' => '1.1.0-dev2'],
                    ['name' => '1.1.1-dev'],
                ],
                ['name' => '1.1.1-dev'],
            ],
            [
                [
                    ['name' => '1.1.0-patch'],
                    ['name' => '1.1.0-patch2'],
                ],
                ['name' => '1.1.0-patch2'],
            ],
            [
                [
                    ['name' => '1.1.0-alpha3'],
                    ['name' => '1.1.0-alpha1'],
                ],
                ['name' => '1.1.0-alpha3'],
            ],
            [
                [
                    ['name' => '1.1.0-beta'],
                    ['name' => '1.1.0-alpha'],
                    ['name' => '1.1.0-beta4'],
                ],
                ['name' => '1.1.0-beta4'],
            ],
            [
                [
                    ['name' => '1.1.0-rc1'],
                    ['name' => '1.1.0-RC'],
                ],
                ['name' => '1.1.0-RC'],
            ]
        ];
    }

    /**
     * Test get last release
     *
     * @dataProvider getTags
     *
     * @param array $list
     * @param array $last
     */
    public function testGetLastRelease(array $list, array $last)
    {
        $this->assertEquals($last, $this->getClient($list)->getLastRelease($this->repository));
    }

    /**
     * Get GitHub client
     *
     * @param array $expected
     *
     * @return \AnimeDb\Bundle\AnimeDbBundle\Client\GitHub
     */
    protected function getClient(array $expected)
    {
        $request = $this->getMock('\Guzzle\Http\Message\RequestInterface');
        $response = $this->getMockBuilder('\Guzzle\Http\Message\Response')
            ->disableOriginalConstructor()
            ->getMock();
        $client = $this->getMock('\Guzzle\Http\Client');
        $client
            ->expects($this->once())
            ->method('setBaseUrl')
            ->willReturnSelf()
            ->with($this->api_host);
        $client
            ->expects($this->once())
            ->method('get')
            ->willReturn($request)
            ->with('repos/'.$this->repository.'/tags');
        $request
            ->expects($this->once())
            ->method('send')
            ->willReturn($response);
        $response
            ->expects($this->once())
            ->method('getBody')
            ->willReturn(json_encode($expected))
            ->with(true);
        return new GitHub($this->api_host, $client);
    }
}