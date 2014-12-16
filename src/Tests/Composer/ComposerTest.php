<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AnimeDbBundle\Tests\Composer;

use AnimeDb\Bundle\AnimeDbBundle\Tests\TestCaseWritable;
use AnimeDb\Bundle\AnimeDbBundle\Composer\Composer;

/**
 * Test composer
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Tests\Composer
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class ComposerTest extends TestCaseWritable
{
    /**
     * Factory
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $factory;

    /**
     * Loader
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $loader;

    /**
     * Lock file
     *
     * @var string
     */
    protected $lock_file;

    /**
     * Composer
     *
     * @var \AnimeDb\Bundle\AnimeDbBundle\Composer\Composer
     */
    protected $composer;

    /**
     * (non-PHPdoc)
     * @see \AnimeDb\Bundle\AnimeDbBundle\Tests\TestCaseWritable::setUp()
     */
    protected function setUp()
    {
        parent::setUp();
        $this->factory = $this->getMock('\Composer\Factory');
        $this->loader = $this->getMock('\Composer\Package\Loader\LoaderInterface');
        $this->lock_file = $this->root_dir.'composer.lock';
        $this->composer = new Composer($this->factory, $this->loader, $this->lock_file);
    }

    /**
     * Test get IO
     */
    public function testGetIO()
    {
        $io = $this->composer->getIO();
        $this->assertInstanceOf('\Composer\IO\NullIO', $io);
        $this->assertEquals($io, $this->composer->getIO());
    }

    /**
     * Test set IO
     */
    public function testSetIO()
    {
        $io = $this->getMock('\Composer\IO\IOInterface');
        $this->composer->setIO($io);
        $this->assertEquals($io, $this->composer->getIO());
    }

    /**
     * Test set IO reload
     */
    public function testSetIOReload()
    {
        $this->getComposer($this->exactly(2));
        $io = $this->getMock('\Composer\IO\IOInterface');

        $this->composer->reload(); // init composer
        $this->composer->setIO($io); // set io and reload composer
        $this->composer->setIO($io); // does nothing
        $this->assertEquals($io, $this->composer->getIO());
    }

    /**
     * Test reload
     */
    public function testReload()
    {
        $this->composer->reload();
        $this->assertFileNotExists($this->lock_file);

        touch($this->lock_file);
        $this->composer->reload();
        $this->assertFileNotExists($this->lock_file);
    }

    /**
     * Test download
     */
    public function testDownload()
    {
        $package = $this->getMock('\Composer\Package\RootPackageInterface');
        $manager = $this->getMockBuilder('\Composer\Downloader\DownloadManager')
            ->disableOriginalConstructor()
            ->getMock();
        $manager
            ->expects($this->once())
            ->method('getDownloaderForInstalledPackage')
            ->willReturnSelf()
            ->with($package);
        $manager
            ->expects($this->once())
            ->method('download')
            ->with($package, $this->root_dir);
        if (defined('PHP_WINDOWS_VERSION_BUILD')) {
            $manager
                ->expects($this->once())
                ->method('setOutputProgress')
                ->willReturnSelf()
                ->with(false);
        }
        $composer = $this->getComposer();
        $composer
            ->expects($this->once())
            ->method('getDownloadManager')
            ->willReturn($manager);

        $this->composer->download($package, $this->root_dir);
    }

    /**
     * Test get root package
     */
    public function testGetRootPackage()
    {
        $package = $this->getMock('\Composer\Package\RootPackageInterface');
        $composer = $this->getComposer();
        $composer
            ->expects($this->once())
            ->method('getPackage')
            ->willReturn($package);

        $this->assertEquals($package, $this->composer->getRootPackage());
    }

    /**
     * Test get package from config file
     */
    public function testGetPackageFromConfigFile()
    {
        $config = $this->root_dir.'composer.json';
        $data = [
            'name' => 'foo/bar',
            'type' => 'library'
        ];
        file_put_contents($config, json_encode($data));
        $package = $this->getMock('\Composer\Package\RootPackageInterface');
        $this->loader
            ->expects($this->once())
            ->method('load')
            ->willReturn($package)
            ->with($data, 'Composer\Package\RootPackage');

        $this->assertEquals($package, $this->composer->getPackageFromConfigFile($config));
    }

    /**
     * Test get package from config file no file
     *
     * @expectedException \RuntimeException
     */
    public function testGetPackageFromConfigFileNoFile()
    {
        $this->loader
            ->expects($this->never())
            ->method('load');
        $this->composer->getPackageFromConfigFile($this->root_dir.'composer.json');
    }

    /**
     * Test get installer
     */
    public function testGetInstaller()
    {
        $package = $this->getMock('\Composer\Package\RootPackageInterface');
        $config = $this->getMock('\Composer\Config');
        $download = $this->getMockBuilder('\Composer\Downloader\DownloadManager')
            ->disableOriginalConstructor()
            ->getMock();
        $repository = $this->getMockBuilder('\Composer\Repository\RepositoryManager')
            ->disableOriginalConstructor()
            ->getMock();
        $locker = $this->getMockBuilder('\Composer\Package\Locker')
            ->disableOriginalConstructor()
            ->getMock();
        $installation = $this->getMock('\Composer\Installer\InstallationManager');
        $event = $this->getMockBuilder('\Composer\EventDispatcher\EventDispatcher')
            ->disableOriginalConstructor()
            ->getMock();
        $autoload = $this->getMockBuilder('\Composer\Autoload\AutoloadGenerator')
            ->disableOriginalConstructor()
            ->getMock();

        if (defined('PHP_WINDOWS_VERSION_BUILD')) {
            $download
                ->expects($this->once())
                ->method('setOutputProgress')
                ->willReturnSelf()
                ->with(false);
        }

        $composer = $this->getComposer();
        $composer
            ->expects($this->once())
            ->method('getConfig')
            ->willReturn($config);
        $composer
            ->expects($this->once())
            ->method('getPackage')
            ->willReturn($package);
        $composer
            ->expects($this->atLeastOnce())
            ->method('getDownloadManager')
            ->willReturn($download);
        $composer
            ->expects($this->once())
            ->method('getRepositoryManager')
            ->willReturn($repository);
        $composer
            ->expects($this->once())
            ->method('getLocker')
            ->willReturn($locker);
        $composer
            ->expects($this->once())
            ->method('getInstallationManager')
            ->willReturn($installation);
        $composer
            ->expects($this->once())
            ->method('getEventDispatcher')
            ->willReturn($event);
        $composer
            ->expects($this->once())
            ->method('getAutoloadGenerator')
            ->willReturn($autoload);

        $installer = $this->composer->getInstaller(); // test

        $this->assertInstanceOf('\Composer\Installer', $installer);
    }

    /**
     * Get versions
     *
     * @return array
     */
    public function getVersions()
    {
        return [
            ['1.0', false],
            ['1.2.3.4', false],
            ['1.2.3', '1.2.3.5.0'],
            ['1.2.3-dev', '1.2.3.1.1'],
            ['1.2.3-dev2', '1.2.3.1.2'],
            ['1.2.3-patch', '1.2.3.2.1'],
            ['1.2.3-patch2', '1.2.3.2.2'],
            ['1.2.3-alpha', '1.2.3.3.1'],
            ['1.2.3-alpha2', '1.2.3.3.2'],
            ['1.2.3-beta', '1.2.3.4.1'],
            ['1.2.3-beta2', '1.2.3.4.2'],
            ['1.2.3-stable', false],
            ['1.2.3-stable2', false],
            ['1.2.3-rc', '1.2.3.6.1'],
            ['1.2.3-rc2', '1.2.3.6.2']
        ];
    }

    /**
     * Test get version compatible
     *
     * @dataProvider getVersions
     *
     * @param string $actual
     * @param string $expected
     */
    public function testGetVersionCompatible($actual, $expected)
    {
        $this->assertEquals($expected, Composer::getVersionCompatible($actual));
    }

    /**
     * Get composer
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation|null $matcher
     */
    protected function getComposer(\PHPUnit_Framework_MockObject_Matcher_Invocation $matcher = null)
    {
        $mock = $this->getMock('\Composer\Composer');
        $that = $this;
        $composer = $this->composer;
        $this->factory
            ->expects($matcher ?: $this->once())
            ->method('createComposer')
            ->willReturnCallback(function ($io) use ($that, $mock, $composer) {
                // check IO from origin composer
                $that->assertEquals($composer->getIO(), $io);
                return $mock;
            });
        return $mock;
    }
}
