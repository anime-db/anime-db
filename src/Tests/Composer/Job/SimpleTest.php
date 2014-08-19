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
use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Config\Remove as RemoveConfig;
use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Kernel\Add as AddKernel;
use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Kernel\Remove as RemoveKernel;
use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Routing\Remove as RemoveRouting;

/**
 * Test job kernel add
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Tests\Composer\Job\Kernel
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class AddTest extends TestCaseWritable
{
    /**
     * Container
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $container;

    /**
     * (non-PHPdoc)
     * @see \AnimeDb\Bundle\AnimeDbBundle\Tests\TestCaseWritable::setUp()
     */
    protected function setUp()
    {
        parent::setUp();
        $this->container = $this->getMock('\AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Container');
    }

    /**
     * Get success add jobs
     *
     * @return array
     */
    public function getSuccessAddJobs()
    {
        return [
            [
                'config',
                $this->getManipulator(
                    '\AnimeDb\Bundle\AnimeDbBundle\Manipulator\Config',
                    'removeResource',
                    ['AnimeDbAnimeDbBundle']
                ),
                $this->never(),
                function ($package, $root_dir) {
                    return new RemoveConfig($package, $root_dir);
                }
            ],
            [
                'kernel',
                $this->getManipulator(
                    '\AnimeDb\Bundle\AnimeDbBundle\Manipulator\Kernel',
                    'addBundle',
                    ['\AnimeDb\Bundle\AnimeDbBundle\AnimeDbAnimeDbBundle']
                ),
                $this->never(),
                function ($package, $root_dir) {
                    return new AddKernel($package, $root_dir);
                }
            ],
            [
                'kernel',
                $this->getManipulator(
                    '\AnimeDb\Bundle\AnimeDbBundle\Manipulator\Kernel',
                    'removeBundle',
                    ['\AnimeDb\Bundle\AnimeDbBundle\AnimeDbAnimeDbBundle']
                ),
                $this->never(),
                function ($package, $root_dir) {
                    return new RemoveKernel($package, $root_dir);
                }
            ],
            [
                'routing',
                $this->getManipulator(
                    '\AnimeDb\Bundle\AnimeDbBundle\Manipulator\Routing',
                    'removeResource',
                    ['foo_bar']
                ),
                $this->once(),
                function ($package, $root_dir) {
                    return new RemoveRouting($package, $root_dir);
                }
            ]
        ];
    }

    /**
     * Test success add in execute
     *
     * @dataProvider getSuccessAddJobs
     *
     * @param string $manipulator_name
     * @param \PHPUnit_Framework_MockObject_MockObject $manipulator
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $matcher
     * @param \Closure $get_job
     */
    public function testSuccessAdd(
        $manipulator_name,
        \PHPUnit_Framework_MockObject_MockObject $manipulator,
        \PHPUnit_Framework_MockObject_Matcher_Invocation $matcher,
        \Closure $get_job
    ) {
        $this->container
            ->expects($this->once())
            ->method('getManipulator')
            ->willReturn($manipulator)
            ->with($manipulator_name);

        $this->execute($get_job, $matcher); // test
    }

    /**
     * Get no add jobs
     *
     * @return array
     */
    public function getNoAddJobs()
    {
        return [
            [
                function ($package, $root_dir) {
                    return new RemoveConfig($package, $root_dir);
                }
            ],
            [
                function ($package, $root_dir) {
                    return new AddKernel($package, $root_dir);
                }
            ],
            [
                function ($package, $root_dir) {
                    return new RemoveKernel($package, $root_dir);
                }
            ]
        ];
    }

    /**
     * Test no add in execute
     *
     * @dataProvider getNoAddJobs
     *
     * @param \Closure $get_job
     */
    public function testNoAdd(\Closure $get_job)
    {
        $this->container
            ->expects($this->never())
            ->method('getManipulator');

        $this->execute($get_job, $this->once(), ''); // test
    }

    /**
     * Execute job
     *
     * @param \Closure $get_job
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $matcher
     * @param string $bundle
     */
    protected function execute(
        \Closure $get_job,
        \PHPUnit_Framework_MockObject_Matcher_Invocation $matcher,
        $bundle = '\AnimeDb\Bundle\AnimeDbBundle\AnimeDbAnimeDbBundle'
    ) {
        $package = $this->getMockBuilder('\Composer\Package\Package')
            ->disableOriginalConstructor()
            ->getMock();
        $package
            ->expects($matcher)
            ->method('getName')
            ->willReturn('foo/bar');
        $package
            ->expects($this->atLeastOnce())
            ->method('getExtra')
            ->willReturn([
                'anime-db-routing' => '',
                'anime-db-config' => '',
                'anime-db-bundle' => $bundle,
                'anime-db-migrations' => ''
            ]);

        $job = $get_job($package, $this->root_dir);
        $job->setContainer($this->container);
        $job->register();
        $job->execute();
    }

    /**
     * Get manipulator
     *
     * @param string $class
     * @param string $method
     * @param array $args
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getManipulator($class, $method, array $args = [])
    {
        $manipulator = $this->getMockBuilder($class)
            ->disableOriginalConstructor()
            ->getMock();
        $invocation = $manipulator
            ->expects($this->once())
            ->method($method);
        call_user_func_array([$invocation, 'with'], $args);
        return $manipulator;
    }
}