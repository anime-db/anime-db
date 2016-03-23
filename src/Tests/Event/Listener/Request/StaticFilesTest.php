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

use AnimeDb\Bundle\AnimeDbBundle\Event\Listener\Request\StaticFiles;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Test static files
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Tests\Event\Listener\Request
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class StaticFilesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $event;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected $fs;

    /**
     * @var string
     */
    protected $root_dir;

    /**
     * @var string
     */
    protected $target_dir;

    protected function setUp()
    {
        $this->fs = new Filesystem();
        $this->event = $this->getMockBuilder('\Symfony\Component\HttpKernel\Event\GetResponseEvent')
            ->disableOriginalConstructor()
            ->getMock();
        $this->root_dir = sys_get_temp_dir().'/tests/target';
        $this->target_dir = sys_get_temp_dir().'/tests/web';
        $this->fs->mkdir([$this->root_dir, $this->target_dir]);
    }

    protected function tearDown()
    {
        $this->fs->remove(sys_get_temp_dir().'/tests/');
    }

    public function testOnKernelRequestIgnore()
    {
        $this->event
            ->expects($this->once())
            ->method('getRequestType')
            ->willReturn(HttpKernelInterface::SUB_REQUEST);
        $this->event
            ->expects($this->never())
            ->method('getRequest');
        $this->handle();
    }

    public function testOnKernelRequestNoFileDev()
    {
        $request = $this->getRequest();
        $request
            ->expects($this->once())
            ->method('getScriptName')
            ->willReturn('/app_dev.php');
        $request
            ->expects($this->once())
            ->method('getPathInfo')
            ->willReturn('/no_file');
        $this->handle();
    }

    public function testOnKernelRequestNoFile()
    {
        $request = $this->getRequest();
        $request
            ->expects($this->atLeastOnce())
            ->method('getScriptName')
            ->willReturn('/no_file');
        $this->handle('prod');
    }

    /**
     * @return array
     */
    public function getFiles()
    {
        return [
            ['/static', 'test', 'text/plain'],
            ['/static', '', 'inode/x-empty'],
            ['/static.css', '', 'text/css'],
            ['/static.js', '', 'text/javascript'],
        ];
    }

    /**
     * @dataProvider getFiles
     *
     * @param string $file
     * @param string $data
     * @param string $mime
     */
    public function testOnKernelRequestDev($file, $data, $mime)
    {
        $that = $this;
        file_put_contents($this->target_dir.$file, $data);
        $request = $this->getRequest();
        $request
            ->expects($this->once())
            ->method('getScriptName')
            ->willReturn('/app_dev.php');
        $request
            ->expects($this->once())
            ->method('getPathInfo')
            ->willReturn($file);
        $this->event
            ->expects($this->once())
            ->method('setResponse')
            ->willReturnCallback(function ($response) use ($that, $data, $mime) {
                $that->assertInstanceOf('\Symfony\Component\HttpFoundation\Response', $response);
                $that->assertEquals($data, $response->getContent());
                $that->assertTrue($response->headers->hasCacheControlDirective('public'));
                $that->assertEquals($mime, $response->headers->get('Content-Type'));
            });
        $this->handle();
    }

    /**
     * @dataProvider getFiles
     *
     * @param string $filename
     * @param string $data
     * @param string $mime
     */
    public function testOnKernelRequest($filename, $data, $mime)
    {
        $that = $this;
        $file = $this->target_dir.$filename;
        file_put_contents($this->target_dir.$filename, $data);
        $request = $this->getRequest();
        $request
            ->expects($this->atLeastOnce())
            ->method('getScriptName')
            ->willReturn($filename);
        $this->event
            ->expects($this->once())
            ->method('setResponse')
            ->willReturnCallback(function ($response) use ($that, $data, $mime, $file) {
                $that->assertInstanceOf('\Symfony\Component\HttpFoundation\Response', $response);
                $that->assertEquals($data, $response->getContent());
                $that->assertTrue($response->headers->hasCacheControlDirective('public'));
                $that->assertEquals($mime, $response->headers->get('Content-Type'));
                $that->assertEquals('"'.md5_file($file).'"', $response->headers->get('ETag'));
                $that->assertTrue($response->headers->getCacheControlDirective('must-revalidate'));
                $that->assertEquals(filemtime($file), $response->getLastModified()->getTimestamp());
                $that->assertInstanceOf('\DateTime', $response->getExpires());
            });
        $this->handle('prod');
    }

    public function testOnKernelRequestCache()
    {
        $that = $this;
        $file = $this->target_dir.'/static';
        file_put_contents($file, 'test');

        $request = $this->getRequest();
        $request->headers = $this->getMock('\Symfony\Component\HttpFoundation\ResponseHeaderBag');
        $request
            ->expects($this->atLeastOnce())
            ->method('getEtags')
            ->willReturn(['*']);
        $request
            ->expects($this->atLeastOnce())
            ->method('getScriptName')
            ->willReturn('/static');
        $request
            ->expects($this->atLeastOnce())
            ->method('isMethodSafe')
            ->willReturn(true);
        $this->event
            ->expects($this->once())
            ->method('setResponse')
            ->willReturnCallback(function ($response) use ($that, $file) {
                $that->assertInstanceOf('\Symfony\Component\HttpFoundation\Response', $response);
                $that->assertEmpty($response->getContent());
                $that->assertTrue($response->headers->hasCacheControlDirective('public'));
                $that->assertEquals('"'.md5_file($file).'"', $response->headers->get('ETag'));
                $that->assertTrue($response->headers->getCacheControlDirective('must-revalidate'));
                $that->assertInstanceOf('\DateTime', $response->getExpires());
            });
        $this->handle('prod');
    }

    /**
     * @param string $env
     */
    protected function handle($env = 'test')
    {
        $listener = new StaticFiles($this->root_dir, $env);
        $listener->onKernelRequest($this->event);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getRequest()
    {
        $request = $this->getMock('\Symfony\Component\HttpFoundation\Request');
        $this->event
            ->expects($this->once())
            ->method('getRequestType')
            ->willReturn(HttpKernelInterface::MASTER_REQUEST);
        $this->event
            ->expects($this->once())
            ->method('getRequest')
            ->willReturn($request);
        return $request;
    }
}
