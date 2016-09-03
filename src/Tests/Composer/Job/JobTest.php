<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\AnimeDbBundle\Tests\Composer\Job;

use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Container;
use AnimeDb\Bundle\AnimeDbBundle\Tests\TestCaseWritable;
use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Job;
use Composer\Package\Package;

class JobTest extends TestCaseWritable
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Package
     */
    protected $package;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Job
     */
    protected $job;

    /**
     * @var array
     */
    protected $default_extra = [
        'anime-db-routing' => '',
        'anime-db-config' => '',
        'anime-db-bundle' => '',
        'anime-db-migrations' => '',
    ];

    /**
     * @param array $extra
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation|null $matcher
     */
    protected function initJob(
        array $extra = [],
        \PHPUnit_Framework_MockObject_Matcher_Invocation $matcher = null
    ) {
        $this->package = $this
            ->getMockBuilder('\Composer\Package\Package')
            ->disableOriginalConstructor()
            ->getMock();
        $this->package
            ->expects($matcher ?: $this->once())
            ->method('getExtra')
            ->will($this->returnValue($extra));
        $this->package
            ->expects($matcher ?: $this->once())
            ->method('setExtra')
            ->with(array_merge($this->default_extra, $extra));
        $this->job = $this
            ->getMockBuilder('\AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Job')
            ->setConstructorArgs([$this->package])
            ->getMockForAbstractClass();
        $this->job->setRootDir($this->root_dir);
    }

    /**
     * @return array
     */
    public function getPackageExtra()
    {
        return [
            [[]],
            [$this->default_extra],
            [
                [
                    'anime-db-routing' => 'my_routing.yml',
                    'anime-db-config' => 'my_config.yml',
                    'anime-db-bundle' => 'Acme\DemoBundle',
                    'anime-db-migrations' => 'my_migrations.yml',
                    'branch-alias' => [
                        'dev-master' => '2.6-dev',
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider getPackageExtra
     *
     * @param array $extra
     */
    public function testConstruct(array $extra)
    {
        $this->initJob($extra);
    }

    /**
     * Test get/set container.
     */
    public function testContainer()
    {
        $this->initJob();
        /* @var $container \PHPUnit_Framework_MockObject_MockObject|Container */
        $container = $this
            ->getMockBuilder('\AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Container')
            ->disableOriginalConstructor()
            ->getMock();
        $this->job->setContainer($container);

        $this->assertEquals($container, $this->job->getContainer());
    }

    public function testGetPackage()
    {
        $this->initJob();
        $this->assertEquals($this->package, $this->job->getPackage());
    }

    public function testGetPriority()
    {
        $this->initJob();
        $this->assertEquals(Job::PRIORITY, $this->job->getPriority());
    }

    public function testGetPackageDir()
    {
        $this->initJob();
        $this->package
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('foo/bar'));

        $this->assertEquals($this->root_dir.'vendor/foo/bar/', $this->job->getPackageDir());
    }

    public function testGetPackageBundleStdClass()
    {
        $this->initJob(['anime-db-bundle' => '\stdClass'], $this->atLeastOnce());
        $this->job->getPackageBundle();
    }

    public function testGetPackageBundleFail()
    {
        $this->initJob([], $this->atLeastOnce());
        $this->package
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('demo-vendor/foo-bar-bundle'));

        $this->assertNull($this->job->getPackageBundle());
    }

    public function testGetPackageBundle()
    {
        $bundle = '\AnimeDb\Bundle\AnimeDbBundle\AnimeDbAnimeDbBundle';
        $this->package = $this
            ->getMockBuilder('\Composer\Package\Package')
            ->disableOriginalConstructor()
            ->getMock();
        $this->package
            ->expects($this->at(0))
            ->method('getExtra')
            ->will($this->returnValue([]));
        $this->package
            ->expects($this->at(1))
            ->method('setExtra')
            ->with($this->default_extra);
        $this->job = $this->getMockForAbstractClass(
            '\AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Job',
            [$this->package, $this->root_dir]
        );
        $this->package
            ->expects($this->at(2))
            ->method('getExtra')
            ->will($this->returnValue($this->default_extra));
        $this->package
            ->expects($this->at(3))
            ->method('setExtra')
            ->with(array_merge($this->default_extra, ['anime-db-bundle' => $bundle]));
        $this->package
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('anime-db/anime-db'));

        $this->assertEquals($bundle, $this->job->getPackageBundle());
    }

    public function testGetPackageCopy()
    {
        $this->initJob([], $this->atLeastOnce());
        $this->package
            ->expects($this->atLeastOnce())
            ->method('getName')
            ->will($this->returnValue('foo/bar'));
        $this->package
            ->expects($this->atLeastOnce())
            ->method('getVersion')
            ->will($this->returnValue('1.0.0'));
        $this->package
            ->expects($this->atLeastOnce())
            ->method('getPrettyVersion')
            ->will($this->returnValue('1'));
        $this->package
            ->expects($this->atLeastOnce())
            ->method('getType')
            ->will($this->returnValue('lib'));

        $copy = $this->job->getPackageCopy(); // test

        $this->assertInstanceOf('\Composer\Package\Package', $copy);
        $this->assertEquals($this->package->getName(), $copy->getName());
        $this->assertEquals($this->package->getVersion(), $copy->getVersion());
        $this->assertEquals($this->package->getPrettyVersion(), $copy->getPrettyVersion());
        $this->assertEquals($this->package->getType(), $copy->getType());
        $this->assertEquals($this->package->getExtra(), $copy->getExtra());
    }

    public function testRegister()
    {
        $this->initJob();
        $this->assertNull($this->job->register());
    }
}
