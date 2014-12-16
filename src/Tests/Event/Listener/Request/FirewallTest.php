<?php

/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AnimeDbBundle\Tests\Event\Listener\Request;

use AnimeDb\Bundle\AnimeDbBundle\Event\Listener\Request\Firewall;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Test firewall
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Tests\Event\Listener\Request
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class FirewallTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Firewall
     *
     * @var \AnimeDb\Bundle\AnimeDbBundle\Event\Listener\Request\Firewall
     */
    protected $listener;

    /**
     * Event
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $event;

    /**
     * (non-PHPdoc)
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        $this->event = $this->getMockBuilder('\Symfony\Component\HttpKernel\Event\GetResponseEvent')
            ->disableOriginalConstructor()
            ->getMock();
        $this->listener = new Firewall();
    }

    /**
     * Test on kernel request ignore
     */
    public function testOnKernelRequestIgnore()
    {
        $this->event
            ->expects($this->once())
            ->method('getRequestType')
            ->willReturn(HttpKernelInterface::SUB_REQUEST);
        $this->event
            ->expects($this->never())
            ->method('getRequest');
        $this->listener->onKernelRequest($this->event);
    }

    /**
     * Get external IPs
     *
     * @return array
     */
    public function getExternalIps()
    {
        return [
            ['HTTP_CLIENT_IP', '255.255.255.255'],
            ['HTTP_X_FORWARDED_FOR', '255.255.255.255'],
            ['REMOTE_ADDR', 'fe21:67cf'],
            ['REMOTE_ADDR', '9.255.255.255'],
            ['REMOTE_ADDR', '11.255.255.255'],
            ['REMOTE_ADDR', '172.32.0.0'],
            ['REMOTE_ADDR', 'bad ip']
        ];
    }

    /**
     * Test on kernel request
     *
     * @dataProvider getExternalIps
     *
     * @param string $header
     * @param string $ip
     */
    public function testOnKernelRequest($header, $ip)
    {
        $that = $this;
        $this->getRequest($header, $ip);
        $this->event
            ->expects($this->once())
            ->method('stopPropagation');
        $this->event
            ->expects($this->once())
            ->method('setResponse')
            ->willReturnCallback(function ($response) use ($that) {
                $that->assertInstanceOf('\Symfony\Component\HttpFoundation\Response', $response);
                $that->assertEquals('You are not allowed to access this application.', $response->getContent());
                $that->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
                $that->assertTrue($response->headers->hasCacheControlDirective('public'));
            });

        $this->listener->onKernelRequest($this->event);
    }

    /**
     * Get local IPs
     *
     * @return array
     */
    public function getLocalIps()
    {
        return [
            ['REMOTE_ADDR', '127.0.0.1'],
            ['REMOTE_ADDR', 'fe80::1'],
            ['REMOTE_ADDR', '::1'],
            ['REMOTE_ADDR', 'fc00::'],
            ['REMOTE_ADDR', 'fc00::fe21:67cf'],
            ['REMOTE_ADDR', '10.0.0.0'],
            ['REMOTE_ADDR', '10.0.0.255'],
            ['REMOTE_ADDR', '10.255.255.255'],
            ['REMOTE_ADDR', '172.16.0.0'],
            ['REMOTE_ADDR', '172.16.0.255'],
            ['REMOTE_ADDR', '172.31.255.255'],
            ['REMOTE_ADDR', '192.168.0.0'],
            ['REMOTE_ADDR', '192.168.0.255'],
            ['REMOTE_ADDR', '192.168.255.255'],
        ];
    }

    /**
     * Test on kernel request local network
     *
     * @dataProvider getLocalIps
     *
     * @param string $header
     * @param string $ip
     */
    public function testOnKernelRequestLocal($header, $ip)
    {
        $this->getRequest($header, $ip);
        $this->event
            ->expects($this->never())
            ->method('stopPropagation');

        $this->listener->onKernelRequest($this->event);
    }

    /**
     * Get request
     *
     * @param string $header
     * @param string $ip
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getRequest($header, $ip)
    {
        $this->event
            ->expects($this->once())
            ->method('getRequestType')
            ->willReturn(HttpKernelInterface::MASTER_REQUEST);
        $request = $this->getMock('\Symfony\Component\HttpFoundation\Request');
        $request->server = $this->getMock('\Symfony\Component\HttpFoundation\ServerBag');
        $request->server
            ->expects($this->atLeastOnce())
            ->method('get')
            ->willReturnCallback(function ($value) use ($header, $ip) {
                return $value == $header ? $ip : null;
            });
        $this->event
            ->expects($this->once())
            ->method('getRequest')
            ->willReturn($request);
        return $request;
    }
}
