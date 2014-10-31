<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AnimeDbBundle\Tests\Composer\Job\Migrate;

use AnimeDb\Bundle\AnimeDbBundle\Tests\TestCaseWritable;
use Symfony\Component\Yaml\Yaml;

/**
 * Test job migrate down
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Tests\Composer\Job\Migrate
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
abstract class TestCase extends TestCaseWritable
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
     * Test no config
     */
    public function testNoConfig()
    {
        $this->execute(); // test
    }

    /**
     * Get migrations
     *
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
     * Put config file
     *
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
                'migrations_directory' => 'DoctrineMigrations'
            ]));
        }
    }

    /**
     * Execute job
     *
     * @param string $migrations
     * @param string $bundle
     */
    protected function execute(
        $migrations = '',
        $bundle = '\AnimeDb\Bundle\AnimeDbBundle\AnimeDbAnimeDbBundle'
    ) {
        $package = $this->getMockBuilder('\Composer\Package\Package')
            ->disableOriginalConstructor()
            ->getMock();
        $package
            ->expects($this->atLeastOnce())
            ->method('getName')
            ->willReturn('foo/bar');
        $package
            ->expects($this->atLeastOnce())
            ->method('getExtra')
            ->willReturn([
                'anime-db-routing' => '',
                'anime-db-config' => '',
                'anime-db-bundle' => $bundle,
                'anime-db-migrations' => $migrations
            ]);

        $job = $this->getJob($package);
        $job->setContainer($this->container);
        $job->register();
        $job->execute();
    }

    /**
     * Get job
     *
     * @param \PHPUnit_Framework_MockObject_MockObject $package
     *
     * @return \AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Migrate\Migrate
     */
    abstract protected function getJob(\PHPUnit_Framework_MockObject_MockObject $package);
}
