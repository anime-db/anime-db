<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AnimeDbBundle\Tests\Composer\Job;

use AnimeDb\Bundle\AnimeDbBundle\Tests\TestCaseWritable;
use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Job;

/**
 * Test job
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Tests\Composer\Job
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class JobTest extends TestCaseWritable
{
    /**
     * Package
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $package;

    /**
     * Job
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $job;

    /**
     * Default extra
     *
     * @var array
     */
    protected $default_extra = [
        'anime-db-routing' => '',
        'anime-db-config' => '',
        'anime-db-bundle' => '',
        'anime-db-migrations' => ''
    ];

    /**
     * Init job
     *
     * @param array $extra
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation|null $matcher
     */
    protected function initJob(
        array $extra = [],
        \PHPUnit_Framework_MockObject_Matcher_Invocation $matcher = null
    ) {
        $this->package = $this->getMockBuilder('\Composer\Package\Package')
            ->disableOriginalConstructor()
            ->getMock();
        $this->package
            ->expects($matcher ?: $this->once())
            ->method('getExtra')
            ->willReturn($extra);
        $this->package
            ->expects($matcher ?: $this->once())
            ->method('setExtra')
            ->with(array_merge($this->default_extra, $extra));
        $this->job = $this->getMockBuilder('\AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Job')
            ->setConstructorArgs([$this->package, $this->root_dir])
            ->getMockForAbstractClass();
    }

    /**
     * Get package extra
     *
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
                        'dev-master' => '2.6-dev'
                    ]
                ]
            ]
        ];
    }

    /**
     * Test construct
     *
     * @dataProvider getPackageExtra
     *
     * @param array $extra
     */
    public function testConstruct(array $extra)
    {
        $this->initJob($extra);
    }

    /**
     * Test get/set container
     */
    public function testContainer()
    {
        $this->initJob();
        $container = $this->getMock('\AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Container');
        $this->job->setContainer($container);
        $this->assertEquals($container, $this->job->getContainer());
    }

    /**
     * Test get package
     */
    public function testGetPackage()
    {
        $this->initJob();
        $this->assertEquals($this->package, $this->job->getPackage());
    }

    /**
     * Test get priority
     */
    public function testGetPriority()
    {
        $this->initJob();
        $this->assertEquals(Job::PRIORITY, $this->job->getPriority());
    }

    /**
     * Get options
     *
     * @return array
     */
    public function getOptions()
    {
        return [
            [
                'anime-db-migrations',
                'my_migrations.yml',
                'getPackageMigrationsFile'
            ],
            [
                'anime-db-config',
                'my_config.yml',
                'getPackageConfigFile'
            ],
            [
                'anime-db-routing',
                'my_routing.yml',
                'getPackageRoutingFile'
            ] 
        ];
    }
    /**
     * Test get package option file
     *
     * @dataProvider getOptions
     *
     * @param string $option
     * @param string $value
     * @param string $method
     */
    public function testGetPackageOptionFile($option, $value, $method)
    {
        // no option
        $this->initJob([], $this->atLeastOnce());
        $this->assertEmpty(call_user_func([$this->job, $method]));

        // no file
        $this->initJob([$option => $value], $this->atLeastOnce());
        $this->package
            ->expects($this->once())
            ->method('getName')
            ->willReturn('foo/bar');
        $this->assertEmpty(call_user_func([$this->job, $method]));

        // all good
        $this->initJob([$option => $value], $this->atLeastOnce());
        $this->package
            ->expects($this->atLeastOnce())
            ->method('getName')
            ->willReturn('foo/bar');
        $this->fs->mkdir($dir = $this->root_dir.'vendor/foo/bar/');
        touch($dir.$value);

        $this->assertEquals($value, call_user_func([$this->job, $method]));
    }

    /**
     * Test get package dir
     */
    public function testGetPackageDir()
    {
        $this->initJob();
        $this->package
            ->expects($this->once())
            ->method('getName')
            ->willReturn('foo/bar');
        $this->assertEquals($this->root_dir.'vendor/foo/bar/', $this->job->getPackageDir());
    }

    /**
     * Test get package bundle stdClass
     */
    public function testGetPackageBundleStdClass()
    {
        $this->initJob(['anime-db-bundle' => '\stdClass'], $this->atLeastOnce());
        $this->job->getPackageBundle();
    }

    /**
     * Test get package bundle fail
     */
    public function testGetPackageBundleFail()
    {
        $this->initJob([], $this->atLeastOnce());
        $this->package
            ->expects($this->once())
            ->method('getName')
            ->willReturn('demo-vendor/foo-bar-bundle');

        $this->assertNull($this->job->getPackageBundle());
    }

    /**
     * Test get package bundle
     */
    public function testGetPackageBundle()
    {
        $bundle = '\AnimeDb\Bundle\AnimeDbBundle\AnimeDbAnimeDbBundle';
        $this->package = $this->getMockBuilder('\Composer\Package\Package')
            ->disableOriginalConstructor()
            ->getMock();
        $this->package
            ->expects($this->at(0))
            ->method('getExtra')
            ->willReturn([]);
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
            ->willReturn($this->default_extra);
        $this->package
            ->expects($this->at(3))
            ->method('setExtra')
            ->with(array_merge($this->default_extra, ['anime-db-bundle' => $bundle]));
        $this->package
            ->expects($this->once())
            ->method('getName')
            ->willReturn('anime-db/anime-db');

        $this->assertEquals($bundle, $this->job->getPackageBundle());
    }

    /**
     * Test get package copy
     */
    public function testGetPackageCopy()
    {
        $this->initJob([], $this->atLeastOnce());
        $this->package
            ->expects($this->atLeastOnce())
            ->method('getName')
            ->willReturn('foo/bar');
        $this->package
            ->expects($this->atLeastOnce())
            ->method('getVersion')
            ->willReturn('1.0.0');
        $this->package
            ->expects($this->atLeastOnce())
            ->method('getPrettyVersion')
            ->willReturn('1');
        $this->package
            ->expects($this->atLeastOnce())
            ->method('getType')
            ->willReturn('lib');

        $copy = $this->job->getPackageCopy(); // test

        $this->assertInstanceOf('\Composer\Package\Package', $copy);
        $this->assertEquals($this->package->getName(), $copy->getName());
        $this->assertEquals($this->package->getVersion(), $copy->getVersion());
        $this->assertEquals($this->package->getPrettyVersion(), $copy->getPrettyVersion());
        $this->assertEquals($this->package->getType(), $copy->getType());
        $this->assertEquals($this->package->getExtra(), $copy->getExtra());
    }

    /**
     * Test register
     */
    public function testRegister()
    {
        $this->initJob();
        $this->assertNull($this->job->register());
    }
}