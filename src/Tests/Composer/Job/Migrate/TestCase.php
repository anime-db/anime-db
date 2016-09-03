<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\AnimeDbBundle\Tests\Composer\Job\Migrate;

use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Migrate\Migrate;
use AnimeDb\Bundle\AnimeDbBundle\Tests\TestCaseWritable;
use Symfony\Component\Yaml\Yaml;
use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Container;
use Composer\Package\Package;

abstract class TestCase extends TestCaseWritable
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Container
     */
    protected $container;

    protected function setUp()
    {
        parent::setUp();
        $this->container = $this
            ->getMockBuilder('\AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Container')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testNoConfig()
    {
        $this->execute(); // test
    }

    /**
     * @return array
     */
    public function getMigrations()
    {
        return [
            ['', 'migrations.yml'],
            ['', 'migrations.xml'],
            ['config/my_migrations.yaml', ''],
        ];
    }

    /**
     * @param string $file
     */
    protected function putConfig($file)
    {
        if (pathinfo($file, PATHINFO_EXTENSION) == 'xml') {
            file_put_contents($file, '<?xml version="1.0" encoding="UTF-8"?>
<doctrine-migrations xmlns="http://doctrine-project.org/schemas/migrations/configuration"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://doctrine-project.org/schemas/migrations/configuration
        http://doctrine-project.org/schemas/migrations/configuration.xsd">
    <migrations-namespace>\Foo\Bundle\BarBundle\FooBarBundle\DoctrineMigrations</migrations-namespace>
    <migrations-directory>DoctrineMigrations</migrations-directory>
</doctrine-migrations>');
        } else {
            file_put_contents($file, Yaml::dump([
                'migrations_namespace' => '\Foo\Bundle\BarBundle\FooBarBundle\DoctrineMigrations',
                'migrations_directory' => 'DoctrineMigrations',
            ]));
        }
    }

    /**
     * @param string $migrations
     * @param string $bundle
     */
    protected function execute(
        $migrations = '',
        $bundle = '\AnimeDb\Bundle\AnimeDbBundle\AnimeDbAnimeDbBundle'
    ) {
        $package = $this
            ->getMockBuilder('\Composer\Package\Package')
            ->disableOriginalConstructor()
            ->getMock();
        $package
            ->expects($this->atLeastOnce())
            ->method('getName')
            ->will($this->returnValue('foo/bar'));
        $package
            ->expects($this->atLeastOnce())
            ->method('getExtra')
            ->will($this->returnValue([
                'anime-db-routing' => '',
                'anime-db-config' => '',
                'anime-db-bundle' => $bundle,
                'anime-db-migrations' => $migrations,
            ]));

        $job = $this->getJob($package);
        $job->setContainer($this->container);
        $job->register();
        $job->execute();
    }

    /**
     * @param \PHPUnit_Framework_MockObject_MockObject|Package $package
     *
     * @return Migrate
     */
    abstract protected function getJob($package);
}
