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
use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Migrate\Up;
use Symfony\Component\Yaml\Yaml;

/**
 * Test job migrate up
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Tests\Composer\Job\Migrate
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class UpTest extends TestCaseWritable
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
    public function setUp()
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
     * Test create proxy migrations
     *
     * @dataProvider getMigrations
     *
     * @param string $config
     */
    public function testCreateProxyMigrations($config, $file)
    {
        $file = $this->root_dir.'vendor/foo/bar/'.($config ?: $file);
        $versions = $this->root_dir.'vendor/foo/bar/DoctrineMigrations/';
        $this->fs->mkdir([dirname($file), $versions, $this->root_dir.'app']);
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
        $version1 = 'Version55555555555555_Demo';
        $version2 = 'Version66666666666666_Test';
        touch($versions.$version1.'.php');
        touch($versions.$version2.'.php');

        $this->execute($config); // test

        $proxy_dir = $this->root_dir.'app/DoctrineMigrations/';
        $this->assertFileExists($proxy_dir.$version1.'.php');
        $this->assertFileExists($proxy_dir.$version2.'.php');
        $this->assertEquals($this->getVersionBody($version1), file_get_contents($proxy_dir.$version1.'.php'));
        $this->assertEquals($this->getVersionBody($version2), file_get_contents($proxy_dir.$version2.'.php'));
    }

    /**
     * Get version body
     *
     * @param string $version
     *
     * @return string
     */
    protected function getVersionBody($version)
    {
        return '<?php
namespace Application\Migrations;

use AnimeDb\Bundle\AnimeDbBundle\DoctrineMigrations\ProxyMigration;

require_once __DIR__."/../../vendor/foo/bar/DoctrineMigrations/'.$version.'.php";

class '.$version.' extends ProxyMigration
{
    protected function getMigration()
    {
        return new \Foo\Bundle\BarBundle\FooBarBundle\DoctrineMigrations\\'.$version.'($this->version);
    }
}';
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

        $job = new Up($package, $this->root_dir);
        $job->setContainer($this->container);
        $job->register();
        $job->execute();
    }
}