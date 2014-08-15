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

use AnimeDb\Bundle\AnimeDbBundle\Composer\ScriptHandler;

/**
 * Test script handler
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Tests\Composer
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class ScriptHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Container
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $container;

    /**
     * Default container
     *
     * @var \AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Container
     */
    protected $default_container;

    /**
     * Command event
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $event_command;

    /**
     * Package event
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $event_package;

    /**
     * Composer
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $composer;

    /**
     * Package
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $package;

    /**
     * (non-PHPdoc)
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        parent::setUp();
        $this->composer = $this->getMock('\Composer\Composer');
        $this->package = $this->getMockBuilder('\Composer\Package\Package')
            ->disableOriginalConstructor()
            ->getMock();
        // this method is called in the job that we did not test
        $this->package
            ->expects($this->any())
            ->method('getExtra')
            ->willReturn([]);
        $this->event_command = $this->getMockBuilder('\Composer\Script\CommandEvent')
            ->disableOriginalConstructor()
            ->getMock();
        $this->event_package = $this->getMockBuilder('\Composer\Script\PackageEvent')
            ->disableOriginalConstructor()
            ->getMock();
        $this->container = $this->getMock('\AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Container');

        $this->default_container = ScriptHandler::getContainer();
        ScriptHandler::setContainer($this->container);

    }

    /**
     * (non-PHPdoc)
     * @see PHPUnit_Framework_TestCase::tearDown()
     */
    protected function tearDown()
    {
        parent::tearDown();
        ScriptHandler::setContainer($this->default_container);
    }

    /**
     * Test get container lazy load
     */
    public function testGetContainerLazyLoad()
    {
        $this->assertEquals($this->container, ScriptHandler::getContainer());
    }

    /**
     * Get data for registr package
     *
     * @return array
     */
    public function getDataFroRegistrPackage()
    {
        return [
            // packageInKernel
            [
                'install',
                'getPackage',
                'packageInKernel',
                '\Composer\DependencyResolver\Operation\InstallOperation',
                '\AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Kernel\Add'
            ],
            [
                'update',
                'getTargetPackage',
                'packageInKernel',
                '\Composer\DependencyResolver\Operation\UpdateOperation',
                '\AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Kernel\Add'
            ],
            [
                'uninstall',
                'getPackage',
                'packageInKernel',
                '\Composer\DependencyResolver\Operation\UninstallOperation',
                '\AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Kernel\Remove'
            ],
            // packageInRouting
            [
                'install',
                'getPackage',
                'packageInRouting',
                '\Composer\DependencyResolver\Operation\InstallOperation',
                '\AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Routing\Add'
            ],
            [
                'update',
                'getTargetPackage',
                'packageInRouting',
                '\Composer\DependencyResolver\Operation\UpdateOperation',
                '\AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Routing\Add'
            ],
            [
                'uninstall',
                'getPackage',
                'packageInRouting',
                '\Composer\DependencyResolver\Operation\UninstallOperation',
                '\AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Routing\Remove'
            ],
            // packageInConfig
            [
                'install',
                'getPackage',
                'packageInConfig',
                '\Composer\DependencyResolver\Operation\InstallOperation',
                '\AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Config\Add'
            ],
            [
                'update',
                'getTargetPackage',
                'packageInConfig',
                '\Composer\DependencyResolver\Operation\UpdateOperation',
                '\AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Config\Add'
            ],
            [
                'uninstall',
                'getPackage',
                'packageInConfig',
                '\Composer\DependencyResolver\Operation\UninstallOperation',
                '\AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Config\Remove'
            ],
            // migratePackage
            [
                'install',
                'getPackage',
                'migratePackage',
                '\Composer\DependencyResolver\Operation\InstallOperation',
                '\AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Migrate\Up'
            ],
            [
                'update',
                'getTargetPackage',
                'migratePackage',
                '\Composer\DependencyResolver\Operation\UpdateOperation',
                '\AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Migrate\Up'
            ],
            [
                'uninstall',
                'getPackage',
                'migratePackage',
                '\Composer\DependencyResolver\Operation\UninstallOperation',
                '\AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Migrate\Down'
            ],
            // notifyPackage
            [
                'install',
                'getPackage',
                'notifyPackage',
                '\Composer\DependencyResolver\Operation\InstallOperation',
                '\AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Notify\Package\Installed'
            ],
            [
                'update',
                'getTargetPackage',
                'notifyPackage',
                '\Composer\DependencyResolver\Operation\UpdateOperation',
                '\AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Notify\Package\Updated'
            ],
            [
                'uninstall',
                'getPackage',
                'notifyPackage',
                '\Composer\DependencyResolver\Operation\UninstallOperation',
                '\AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Notify\Package\Removed'
            ],
        ];
    }

    /**
     * Test registr package
     *
     * @dataProvider getDataFroRegistrPackage
     *
     * @param string $type
     * @param string $method
     * @param string $test
     * @param string $operation_class
     * @param string $job_class
     */
    public function testRegistrPackage($type, $method, $test, $operation_class, $job_class)
    {
        $operation = $this->getMockBuilder($operation_class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->event_package
            ->expects($this->atLeastOnce())
            ->method('getOperation')
            ->willReturn($operation);
        $operation
            ->expects($this->once())
            ->method('getJobType')
            ->willReturn($type);
        $operation
            ->expects($this->once())
            ->method($method)
            ->willReturn($this->package);
        $that = $this;
        $package = $this->package;
        $this->container
            ->expects($this->once())
            ->method('addJob')
            ->willReturnCallback(function ($job) use ($that, $package, $job_class) {
                $that->assertInstanceOf($job_class, $job);
                $that->assertEquals($package, $job->getPackage());
            });

        call_user_func(['\AnimeDb\Bundle\AnimeDbBundle\Composer\ScriptHandler', $test], $this->event_package);
    }

    /**
     * Get data from registr package undefined type
     *
     * @return array
     */
    public function getDataFromRegistrPackageUndefinedType()
    {
        return [
            ['packageInKernel'],
            ['packageInRouting'],
            ['packageInConfig'],
            ['migratePackage'],
            ['notifyPackage'],
        ];
    }

    /**
     * Test registr package undefined job type
     *
     * @dataProvider getDataFromRegistrPackageUndefinedType
     *
     * @param string $method
     */
    public function testRegistrPackageUndefinedType($method)
    {
        $operation = $this->getMock('\Composer\DependencyResolver\Operation\OperationInterface');
        $this->event_package
            ->expects($this->atLeastOnce())
            ->method('getOperation')
            ->willReturn($operation);
        $operation
            ->expects($this->once())
            ->method('getJobType')
            ->willReturn('undefined');
        $operation
            ->expects($this->never())
            ->method('getPackage');
        $operation
            ->expects($this->never())
            ->method('getTargetPackage');

        call_user_func(['\AnimeDb\Bundle\AnimeDbBundle\Composer\ScriptHandler', $method], $this->event_package);
    }

    /**
     * Test notify project install
     */
    public function testNotifyProjectInstall()
    {
        $this->getRootPackage();
        $that = $this;
        $package = $this->package;
        $this->container
            ->expects($this->once())
            ->method('addJob')
            ->willReturnCallback(function ($job) use ($that, $package) {
                $that->assertInstanceOf('\AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Notify\Project\Installed', $job);
                $that->assertEquals($package, $job->getPackage());
            });
        ScriptHandler::notifyProjectInstall($this->event_command);
    }

    /**
     * Test notify project update
     */
    public function testNotifyProjectUpdate()
    {
        $this->getRootPackage();
        $that = $this;
        $package = $this->package;
        $this->container
            ->expects($this->once())
            ->method('addJob')
            ->willReturnCallback(function ($job) use ($that, $package) {
                $that->assertInstanceOf('\AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Notify\Project\Updated', $job);
                $that->assertEquals($package, $job->getPackage());
            });
        ScriptHandler::notifyProjectUpdate($this->event_command);
    }

    /**
     * Get root package for job
     */
    protected function getRootPackage()
    {
        $this->event_command
            ->expects($this->once())
            ->method('getComposer')
            ->willReturn($this->composer);
        $this->composer
            ->expects($this->once())
            ->method('getPackage')
            ->willReturn($this->package);
    }

    /**
     * Test exec jobs
     */
    public function testExecJobs()
    {
        $this->container
            ->expects($this->once())
            ->method('execute');
        ScriptHandler::execJobs();
    }

    /**
     * Test deliver events
     */
    public function testDeliverEvents()
    {
        $io = $this->getMock('\Composer\IO\IOInterface');
        $this->event_command
            ->expects($this->any())
            ->method('getIO')
            ->willReturn($io);
        $io
            ->expects($this->at(0))
            ->method('isDecorated')
            ->willReturn(false);
        $io
            ->expects($this->at(1))
            ->method('isDecorated')
            ->willReturn(true);
        $this->container
            ->expects($this->at(0))
            ->method('executeCommand')
            ->with('animedb:deliver-events', null);
        $this->container
            ->expects($this->at(1))
            ->method('executeCommand')
            ->with('animedb:deliver-events --ansi', null);

        ScriptHandler::deliverEvents($this->event_command);
        ScriptHandler::deliverEvents($this->event_command);
    }

    /**
     * Test dump assets
     */
    public function testDumpAssets()
    {
        $io = $this->getMock('\Composer\IO\IOInterface');
        $this->event_command
            ->expects($this->any())
            ->method('getIO')
            ->willReturn($io);
        $io
            ->expects($this->at(0))
            ->method('isDecorated')
            ->willReturn(false);
        $io
            ->expects($this->at(1))
            ->method('isDecorated')
            ->willReturn(true);
        $this->container
            ->expects($this->at(0))
            ->method('executeCommand')
            ->with('assetic:dump --env=prod --no-debug --force web', null);
        $this->container
            ->expects($this->at(1))
            ->method('executeCommand')
            ->with('assetic:dump --env=prod --no-debug --force --ansi web', null);

        ScriptHandler::dumpAssets($this->event_command);
        ScriptHandler::dumpAssets($this->event_command);
    }

    /**
     * Test add package to kernel
     */
    public function testAddPackageToKernel()
    {
        $this->clearCache(0, 'prod');
        $this->clearCache(1, 'test');
        $this->clearCache(2, 'dev');
        ScriptHandler::clearCache();
    }

    /**
     * Clear cache
     * @param integer $index
     * @param string $env
     */
    protected function clearCache($index, $env)
    {
        $this->container
            ->expects($this->at($index))
            ->method('executeCommand')
            ->with('cache:clear --no-warmup --env='.$env.' --no-debug', 0);
    }
}